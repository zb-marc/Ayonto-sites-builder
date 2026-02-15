<?php
/**
 * Help Admin Page
 *
 * @package    Ayonto_Sites
 * @subpackage Admin
 * @since      0.1.47
 */

namespace Ayonto\Sites\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Help class
 *
 * Documentation Center - Displays BUILD-*.md files and documentation
 *
 * @since 0.1.47
 */
class Help {

	/**
	 * Instance
	 *
	 * @var Help
	 */
	private static $instance = null;

	/**
	 * Plugin settings
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Parsedown instance
	 *
	 * @var \Parsedown
	 */
	private $parsedown;

	/**
	 * Get instance
	 *
	 * @return Help
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->settings = array(
			'plugin_name' => 'Ayonto Sites Builder',
			'version'     => AYONTO_SITES_VERSION,
			'build'       => AYONTO_SITES_BUILD,
		);
		
		// Initialize Parsedown if available.
		if ( class_exists( 'Parsedown' ) ) {
			$this->parsedown = new \Parsedown();
		}
		
		// Register hooks.
		add_action( 'admin_menu', array( $this, 'register_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register admin menu
	 */
	public function register_menu() {
		add_submenu_page(
			'ayonto-root',
			__( 'Hilfe & Dokumentation', 'ayonto-sites' ),
			__( 'Hilfe', 'ayonto-sites' ),
			'manage_options',
			'ayonto-help',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'ayonto_page_ayonto-help' !== $hook ) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'ayonto-help',
			plugin_dir_url( __FILE__ ) . '../../assets/css/admin-help.css',
			array(),
			'0.1.47'
		);

		// JS
		wp_enqueue_script(
			'ayonto-help',
			plugin_dir_url( __FILE__ ) . '../../assets/js/admin-help.js',
			array( 'jquery' ),
			'0.1.47',
			true
		);

		// Highlight.js für Code-Syntax-Highlighting
		wp_enqueue_style(
			'highlightjs',
			'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css',
			array(),
			'11.9.0'
		);

		wp_enqueue_script(
			'highlightjs',
			'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js',
			array(),
			'11.9.0',
			true
		);
	}

	/**
	 * Render help page
	 */
	public function render_page() {
		// Check nonce if form submitted
		if ( ! empty( $_POST ) && ( ! isset( $_POST['vt_help_nonce'] ) || ! wp_verify_nonce( $_POST['vt_help_nonce'], 'vt_help_search' ) ) ) {
			wp_die( esc_html__( 'Sicherheitsprüfung fehlgeschlagen.', 'ayonto-sites' ) );
		}

		$search_query = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$current_doc  = isset( $_GET['doc'] ) ? sanitize_file_name( wp_unslash( $_GET['doc'] ) ) : '';

		$docs = $this->get_documentation_files();

		// If no doc selected, show first one
		if ( empty( $current_doc ) && ! empty( $docs ) ) {
			$current_doc = basename( $docs[0], '.md' );
		}

		?>
		<div class="wrap vt-help-wrap">
			<h1>
				<span class="dashicons dashicons-book-alt"></span>
				<?php echo esc_html( $this->settings['plugin_name'] ); ?> - <?php esc_html_e( 'Hilfe & Dokumentation', 'ayonto-sites' ); ?>
			</h1>

			<div class="vt-help-container">
				<!-- Sidebar -->
				<div class="vt-help-sidebar">
					<div class="vt-help-search">
						<form method="get" action="">
							<input type="hidden" name="page" value="ayonto-help">
							<?php wp_nonce_field( 'vt_help_search', 'vt_help_nonce' ); ?>
							<input type="search" 
								name="s" 
								value="<?php echo esc_attr( $search_query ); ?>"
								placeholder="<?php esc_attr_e( 'Dokumentation durchsuchen...', 'ayonto-sites' ); ?>"
								class="vt-search-input">
							<button type="submit" class="vt-search-btn">
								<span class="dashicons dashicons-search"></span>
							</button>
						</form>
					</div>

					<div class="vt-help-nav">
						<h3><?php esc_html_e( 'Dokumentationen', 'ayonto-sites' ); ?></h3>
						<ul>
							<?php foreach ( $docs as $doc_path ) : ?>
								<?php
								$doc_name = basename( $doc_path, '.md' );
								$doc_info = $this->get_doc_info( $doc_path );
								$is_active = ( $doc_name === $current_doc );
								?>
								<li class="<?php echo $is_active ? 'active' : ''; ?>">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=ayonto-help&doc=' . urlencode( $doc_name ) ) ); ?>">
										<?php echo $this->get_doc_icon( $doc_name ); ?>
										<span class="doc-title"><?php echo esc_html( $doc_info['title'] ); ?></span>
										<?php if ( ! empty( $doc_info['build'] ) ) : ?>
											<span class="doc-build"><?php echo esc_html( $doc_info['build'] ); ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="vt-help-info">
						<h4><?php esc_html_e( 'Plugin-Information', 'ayonto-sites' ); ?></h4>
						<dl>
							<dt><?php esc_html_e( 'Version:', 'ayonto-sites' ); ?></dt>
							<dd><?php echo esc_html( $this->settings['version'] ); ?></dd>
							<dt><?php esc_html_e( 'Build:', 'ayonto-sites' ); ?></dt>
							<dd><?php echo esc_html( $this->settings['build'] ); ?></dd>
						</dl>
					</div>
				</div>

				<!-- Content -->
				<div class="vt-help-content">
					<?php if ( ! empty( $search_query ) ) : ?>
						<?php $this->render_search_results( $search_query, $docs ); ?>
					<?php else : ?>
						<?php $this->render_document( $current_doc, $docs ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get all documentation files
	 *
	 * @return array Array of file paths.
	 */
	private function get_documentation_files() {
		$plugin_dir = plugin_dir_path( dirname( dirname( __FILE__ ) ) );
		$files      = array();

		// Priority order
		$priority_files = array(
			'README.md',
			'UPDATE.md',
			'TESTING.md',
		);

		// Add priority files if they exist
		foreach ( $priority_files as $file ) {
			$path = $plugin_dir . $file;
			if ( file_exists( $path ) ) {
				$files[] = $path;
			}
		}

		// BUILD-*.md files are NOT included in documentation
		// They are development artifacts and should not be shown to users
		// Only show: README.md (Overview), UPDATE.md (Changelog), TESTING.md (Test Guide)

		return $files;
	}

	/**
	 * Get document info (title, build number)
	 *
	 * @param string $file_path Path to markdown file.
	 * @return array Document info.
	 */
	private function get_doc_info( $file_path ) {
		$filename = basename( $file_path, '.md' );
		$title    = $filename;
		$build    = '';

		// Extract build number for BUILD-*.md files
		if ( preg_match( '/BUILD-(\d+)/', $filename, $matches ) ) {
			$build = 'Build ' . $matches[1];
			$title = 'Build ' . $matches[1];

			// Try to get version from file content
			$content = file_get_contents( $file_path );
			if ( preg_match( '/^#\s+(.+)/m', $content, $title_match ) ) {
				$title = trim( $title_match[1] );
			}
		} elseif ( $filename === 'README' ) {
			$title = __( 'Übersicht', 'ayonto-sites' );
		} elseif ( $filename === 'UPDATE' ) {
			$title = __( 'Changelog', 'ayonto-sites' );
		} elseif ( $filename === 'TESTING' ) {
			$title = __( 'Testing-Guide', 'ayonto-sites' );
		}

		return array(
			'title' => $title,
			'build' => $build,
		);
	}

	/**
	 * Get icon for document type
	 *
	 * @param string $doc_name Document name.
	 * @return string HTML icon.
	 */
	private function get_doc_icon( $doc_name ) {
		$icons = array(
			'README'  => 'dashicons-info',
			'UPDATE'  => 'dashicons-update',
			'TESTING' => 'dashicons-admin-tools',
		);

		foreach ( $icons as $key => $icon ) {
			if ( stripos( $doc_name, $key ) !== false ) {
				return '<span class="dashicons ' . esc_attr( $icon ) . '"></span>';
			}
		}

		// Default: BUILD icon
		return '<span class="dashicons dashicons-media-document"></span>';
	}

	/**
	 * Render document content
	 *
	 * @param string $doc_name Document name.
	 * @param array  $docs     Array of all documents.
	 */
	private function render_document( $doc_name, $docs ) {
		$file_path = null;
		foreach ( $docs as $doc_path ) {
			if ( basename( $doc_path, '.md' ) === $doc_name ) {
				$file_path = $doc_path;
				break;
			}
		}

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			echo '<div class="notice notice-error"><p>';
			esc_html_e( 'Dokumentation nicht gefunden.', 'ayonto-sites' );
			echo '</p></div>';
			return;
		}

		$content  = file_get_contents( $file_path );
		$doc_info = $this->get_doc_info( $file_path );

		// Parse Markdown to HTML
		$html = $this->parsedown->text( $content );

		// Add wrapper for better styling
		$html = '<div class="vt-markdown-content">' . $html . '</div>';

		// Document header
		echo '<div class="vt-doc-header">';
		echo '<h2>' . esc_html( $doc_info['title'] ) . '</h2>';
		if ( ! empty( $doc_info['build'] ) ) {
			echo '<span class="vt-doc-badge">' . esc_html( $doc_info['build'] ) . '</span>';
		}
		echo '<div class="vt-doc-meta">';
		echo '<span class="vt-doc-filename"><span class="dashicons dashicons-media-document"></span> ' . esc_html( basename( $file_path ) ) . '</span>';
		$modified = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), filemtime( $file_path ) );
		echo '<span class="vt-doc-date"><span class="dashicons dashicons-clock"></span> ' . esc_html( $modified ) . '</span>';
		echo '</div>';
		echo '</div>';

		// Content
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Parsedown output is sanitized
		echo $html;

		// Navigation
		$this->render_doc_navigation( $doc_name, $docs );
	}

	/**
	 * Render document navigation (prev/next)
	 *
	 * @param string $current_doc Current document name.
	 * @param array  $docs        Array of all documents.
	 */
	private function render_doc_navigation( $current_doc, $docs ) {
		$current_index = null;
		foreach ( $docs as $index => $doc_path ) {
			if ( basename( $doc_path, '.md' ) === $current_doc ) {
				$current_index = $index;
				break;
			}
		}

		if ( $current_index === null ) {
			return;
		}

		$prev_doc = isset( $docs[ $current_index + 1 ] ) ? $docs[ $current_index + 1 ] : null;
		$next_doc = isset( $docs[ $current_index - 1 ] ) ? $docs[ $current_index - 1 ] : null;

		if ( ! $prev_doc && ! $next_doc ) {
			return;
		}

		echo '<div class="vt-doc-nav">';

		if ( $prev_doc ) {
			$prev_name = basename( $prev_doc, '.md' );
			$prev_info = $this->get_doc_info( $prev_doc );
			echo '<a href="' . esc_url( admin_url( 'admin.php?page=ayonto-help&doc=' . urlencode( $prev_name ) ) ) . '" class="vt-doc-nav-prev">';
			echo '<span class="dashicons dashicons-arrow-left-alt2"></span>';
			echo '<span>' . esc_html( $prev_info['title'] ) . '</span>';
			echo '</a>';
		} else {
			echo '<span></span>'; // Spacer
		}

		if ( $next_doc ) {
			$next_name = basename( $next_doc, '.md' );
			$next_info = $this->get_doc_info( $next_doc );
			echo '<a href="' . esc_url( admin_url( 'admin.php?page=ayonto-help&doc=' . urlencode( $next_name ) ) ) . '" class="vt-doc-nav-next">';
			echo '<span>' . esc_html( $next_info['title'] ) . '</span>';
			echo '<span class="dashicons dashicons-arrow-right-alt2"></span>';
			echo '</a>';
		}

		echo '</div>';
	}

	/**
	 * Render search results
	 *
	 * @param string $query Search query.
	 * @param array  $docs  Array of all documents.
	 */
	private function render_search_results( $query, $docs ) {
		echo '<div class="vt-search-results">';
		echo '<h2>';
		echo '<span class="dashicons dashicons-search"></span> ';
		/* translators: %s: search query */
		printf( esc_html__( 'Suchergebnisse für "%s"', 'ayonto-sites' ), esc_html( $query ) );
		echo '</h2>';

		$results = array();
		$query_lower = strtolower( $query );

		foreach ( $docs as $doc_path ) {
			$content  = file_get_contents( $doc_path );
			$doc_info = $this->get_doc_info( $doc_path );
			$doc_name = basename( $doc_path, '.md' );

			// Search in content
			if ( stripos( $content, $query ) !== false ) {
				// Find matching lines
				$lines   = explode( "\n", $content );
				$matches = array();

				foreach ( $lines as $line_num => $line ) {
					if ( stripos( $line, $query ) !== false ) {
						// Get context (line before and after)
						$context_start = max( 0, $line_num - 1 );
						$context_end   = min( count( $lines ) - 1, $line_num + 1 );
						$context       = array_slice( $lines, $context_start, $context_end - $context_start + 1 );

						// Highlight query in line
						$highlighted_line = preg_replace(
							'/(' . preg_quote( $query, '/' ) . ')/i',
							'<mark>$1</mark>',
							$line
						);

						$matches[] = array(
							'line'    => $line_num + 1,
							'content' => $highlighted_line,
							'context' => implode( "\n", $context ),
						);

						// Limit to 3 matches per file
						if ( count( $matches ) >= 3 ) {
							break;
						}
					}
				}

				if ( ! empty( $matches ) ) {
					$results[] = array(
						'doc_path' => $doc_path,
						'doc_name' => $doc_name,
						'doc_info' => $doc_info,
						'matches'  => $matches,
					);
				}
			}
		}

		if ( empty( $results ) ) {
			echo '<div class="notice notice-warning"><p>';
			esc_html_e( 'Keine Ergebnisse gefunden.', 'ayonto-sites' );
			echo '</p></div>';
		} else {
			/* translators: %d: number of results */
			echo '<p class="vt-search-count">' . sprintf( esc_html__( '%d Dokumente gefunden', 'ayonto-sites' ), count( $results ) ) . '</p>';

			foreach ( $results as $result ) {
				echo '<div class="vt-search-result">';
				echo '<h3>';
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=ayonto-help&doc=' . urlencode( $result['doc_name'] ) ) ) . '">';
				echo esc_html( $result['doc_info']['title'] );
				echo '</a>';
				if ( ! empty( $result['doc_info']['build'] ) ) {
					echo ' <span class="vt-doc-badge">' . esc_html( $result['doc_info']['build'] ) . '</span>';
				}
				echo '</h3>';

				foreach ( $result['matches'] as $match ) {
					echo '<div class="vt-search-match">';
					/* translators: %d: line number */
					echo '<span class="vt-match-line">' . sprintf( esc_html__( 'Zeile %d', 'ayonto-sites' ), $match['line'] ) . '</span>';
					echo '<div class="vt-match-content">';
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped with esc_html in highlighting
					echo wp_kses_post( $match['content'] );
					echo '</div>';
					echo '</div>';
				}

				echo '</div>';
			}
		}

		echo '</div>';
	}
}
