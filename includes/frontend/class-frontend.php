<?php
/**
 * Frontend functionality
 *
 * @package    Voltrana_Sites
 * @subpackage Frontend
 * @since      0.1.0
 */

namespace Voltrana\Sites\Frontend;

use Voltrana\Sites\Admin\Settings_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend class
 *
 * Handles frontend assets and functionality.
 *
 * @since 0.1.0
 */
class Frontend {
	
	/**
	 * Singleton instance
	 *
	 * @var Frontend
	 */
	private static $instance = null;
	
	/**
	 * Get singleton instance
	 *
	 * @return Frontend
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'output_custom_css' ), 99 );
		
		// Build 056: Prevent duplicate featured image output on single battery pages.
		add_filter( 'post_thumbnail_html', array( $this, 'maybe_remove_featured_image' ), 10, 5 );
	}
	
	/**
	 * Enqueue frontend scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Always register frontend styles so shortcodes can enqueue them.
		wp_register_style(
			'voltrana-frontend',
			VOLTRANA_SITES_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			VOLTRANA_SITES_VERSION
		);

		// Build 055: Register GLightbox CSS.
		wp_register_style(
			'voltrana-glightbox',
			VOLTRANA_SITES_PLUGIN_URL . 'assets/css/glightbox.min.css',
			array(),
			'3.3.0'
		);

		// Build 055: Register GLightbox JS (LOCAL).
		wp_register_script(
			'voltrana-glightbox',
			VOLTRANA_SITES_PLUGIN_URL . 'assets/js/glightbox.min.js',
			array(),
			'3.3.0',
			true
		);

		// Build 055: Register GLightbox Init Script.
		wp_register_script(
			'voltrana-glightbox-init',
			VOLTRANA_SITES_PLUGIN_URL . 'assets/js/glightbox-init.js',
			array( 'voltrana-glightbox' ),
			VOLTRANA_SITES_VERSION,
			true
		);

		// Auto-enqueue on battery pages.
		if ( is_singular( 'vt_battery' ) || is_post_type_archive( 'vt_battery' ) || is_tax( 'vt_category' ) ) {
			wp_enqueue_style( 'voltrana-frontend' );
			wp_enqueue_style( 'voltrana-glightbox' );
			wp_enqueue_script( 'voltrana-glightbox' );
			wp_enqueue_script( 'voltrana-glightbox-init' );
		}
	}

	/**
	 * Output custom CSS with color variables from settings
	 *
	 * @return void
	 */
	public function output_custom_css() {
		?>
		<style id="voltrana-custom-colors">
			<?php echo Settings_Helper::get_css_variables(); ?>
			
			/* Apply colors to plugin elements */
			.vt-battery-list .vt-battery-item:hover {
				border-color: var(--vt-primary);
			}
			
			.vt-filters .vt-filter-button.active {
				background-color: var(--vt-primary);
				color: #fff;
			}
			
			.vt-battery-table th {
				background-color: var(--vt-secondary);
				color: #fff;
			}
			
			.vt-battery-table tbody tr:hover {
				background-color: rgba(0, 75, 97, 0.05);
			}
			
			.vt-button-primary {
				background-color: var(--vt-accent);
				border-color: var(--vt-accent);
			}
			
			.vt-button-primary:hover {
				opacity: 0.9;
			}
			
			.vt-border {
				border-color: var(--vt-border);
			}
		</style>
		<?php
	}
	
	/**
	 * Maybe remove featured image from single battery pages
	 * 
	 * Build 056: Prevents duplicate featured image output when theme automatically
	 * displays featured images on single post pages. The image should only be shown
	 * where explicitly placed in Elementor templates or content.
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param int          $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size.
	 * @param string|array $attr              Query string or array of attributes.
	 * @return string Empty string for vt_battery posts, original HTML otherwise.
	 */
	public function maybe_remove_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		// Only apply on single battery pages.
		if ( is_singular( 'vt_battery' ) ) {
			// Check if this is being called in the main loop/content area.
			// We want to keep featured images in Elementor widgets/templates.
			// The theme's automatic output usually happens in the_content filter chain.
			if ( in_the_loop() && is_main_query() ) {
				return ''; // Remove featured image from theme's automatic output.
			}
		}
		
		return $html;
	}
}