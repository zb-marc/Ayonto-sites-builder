<?php
/**
 * Dashboard functionality
 *
 * @package    Ayonto_Sites
 * @subpackage Admin
 * @since      0.1.41
 */

namespace Ayonto\Sites\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard class
 *
 * Provides admin dashboard with statistics, quick actions, and system info.
 *
 * @since 0.1.41
 */
class Dashboard {
	
	/**
	 * Singleton instance
	 *
	 * @var Dashboard
	 */
	private static $instance = null;
	
	/**
	 * Get singleton instance
	 *
	 * @return Dashboard
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
		$this->init_hooks();
	}
	
	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}
	
	/**
	 * Add dashboard page to admin menu
	 *
	 * @return void
	 */
	public function add_dashboard_page() {
		add_submenu_page(
			'ayonto-root',
			__( 'Dashboard', 'ayonto-sites' ),
			__( 'Dashboard', 'ayonto-sites' ),
			'manage_options',
			'ayonto-dashboard',
			array( $this, 'render_page' ),
			0 // Position: First submenu item
		);
	}
	
	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_ayonto-root' !== $hook && 'ayonto_page_ayonto-dashboard' !== $hook ) {
			return;
		}
		
		// Enqueue Ayonto Admin CSS (unified design system).
		wp_enqueue_style(
			'ayonto-admin',
			AYONTO_SITES_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			AYONTO_SITES_VERSION
		);
		
		// Enqueue Dashboard-specific CSS.
		wp_enqueue_style(
			'ayonto-dashboard',
			AYONTO_SITES_PLUGIN_URL . 'assets/css/admin-dashboard.css',
			array( 'ayonto-admin' ),
			AYONTO_SITES_VERSION
		);
	}
	
	/**
	 * Render dashboard page
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="wrap vt-dashboard">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<div class="vt-dashboard-widgets">
				<!-- Quick Actions Widget -->
				<div class="vt-widget vt-quick-actions">
					<?php $this->render_quick_actions(); ?>
				</div>
				
				<!-- Recent Activity Widget -->
				<div class="vt-widget vt-recent">
					<?php $this->render_recent_activity(); ?>
				</div>
				
				<!-- System Status Widget -->
				<div class="vt-widget vt-system">
					<?php $this->render_system_status(); ?>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render statistics widget
	 *
	 * @return void
	 */
	private function render_statistics() {
		global $wpdb;
		
		// Get total batteries count.
		$total_batteries = wp_count_posts( 'vt_battery' );
		$published = isset( $total_batteries->publish ) ? $total_batteries->publish : 0;
		$draft = isset( $total_batteries->draft ) ? $total_batteries->draft : 0;
		
		// Get count by technology.
		$tech_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value as technology, COUNT(*) as count
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE pm.meta_key = %s
				AND p.post_type = %s
				AND p.post_status = %s
				AND pm.meta_value != ''
				AND pm.meta_value IS NOT NULL
				GROUP BY pm.meta_value
				ORDER BY count DESC",
				'technology',
				'vt_battery',
				'publish'
			)
		);
		
		// Get count by brand (NEW!).
		$brand_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value as brand, COUNT(*) as count
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE pm.meta_key = %s
				AND p.post_type = %s
				AND p.post_status = %s
				AND pm.meta_value != ''
				AND pm.meta_value IS NOT NULL
				GROUP BY pm.meta_value
				ORDER BY count DESC
				LIMIT 5",
				'brand',
				'vt_battery',
				'publish'
			)
		);
		
		// Get count by voltage.
		$voltage_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value as voltage, COUNT(*) as count
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE pm.meta_key = %s
				AND p.post_type = %s
				AND p.post_status = %s
				AND pm.meta_value != ''
				AND pm.meta_value IS NOT NULL
				AND pm.meta_value != '0'
				GROUP BY pm.meta_value
				ORDER BY CAST(meta_value AS UNSIGNED)",
				'voltage_v',
				'vt_battery',
				'publish'
			)
		);
		
		// Get capacity statistics (NEW!).
		$capacity_stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT 
					MIN(CAST(meta_value AS DECIMAL(10,2))) as min_capacity,
					MAX(CAST(meta_value AS DECIMAL(10,2))) as max_capacity,
					AVG(CAST(meta_value AS DECIMAL(10,2))) as avg_capacity
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE pm.meta_key = %s
				AND p.post_type = %s
				AND p.post_status = %s
				AND pm.meta_value != ''
				AND pm.meta_value IS NOT NULL
				AND CAST(meta_value AS DECIMAL(10,2)) > 0",
				'capacity_ah',
				'vt_battery',
				'publish'
			)
		);
		
		// Get category count.
		$cat_count = wp_count_terms( array(
			'taxonomy'   => 'vt_category',
			'hide_empty' => false,
		) );
		
		?>
		<h2><?php esc_html_e( 'Statistiken', 'ayonto-sites' ); ?></h2>
		
		<div class="vt-stat-item vt-stat-total">
			<span class="vt-stat-label"><?php esc_html_e( 'Lösungen gesamt', 'ayonto-sites' ); ?></span>
			<span class="vt-stat-value"><?php echo esc_html( $published ); ?></span>
		</div>
		
		<?php if ( $draft > 0 ) : ?>
		<div class="vt-stat-item vt-stat-draft">
			<span class="vt-stat-label"><?php esc_html_e( 'Entwürfe', 'ayonto-sites' ); ?></span>
			<span class="vt-stat-value"><?php echo esc_html( $draft ); ?></span>
		</div>
		<?php endif; ?>
		
		<div class="vt-stat-item">
			<span class="vt-stat-label"><?php esc_html_e( 'Kategorien', 'ayonto-sites' ); ?></span>
			<span class="vt-stat-value"><?php echo esc_html( $cat_count ); ?></span>
		</div>
		
		<?php if ( ! empty( $tech_counts ) ) : ?>
			<h3><?php esc_html_e( 'Nach Technologie', 'ayonto-sites' ); ?></h3>
			<?php foreach ( $tech_counts as $tech ) : ?>
				<div class="vt-stat-item vt-stat-small">
					<span class="vt-stat-label"><?php echo esc_html( $tech->technology ); ?></span>
					<span class="vt-stat-value"><?php echo esc_html( $tech->count ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php if ( ! empty( $brand_counts ) ) : ?>
			<h3><?php esc_html_e( 'Top 5 Marken', 'ayonto-sites' ); ?></h3>
			<?php foreach ( $brand_counts as $brand ) : ?>
				<div class="vt-stat-item vt-stat-small">
					<span class="vt-stat-label"><?php echo esc_html( $brand->brand ); ?></span>
					<span class="vt-stat-value"><?php echo esc_html( $brand->count ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php if ( ! empty( $voltage_counts ) ) : ?>
			<h3><?php esc_html_e( 'Nach Spannung', 'ayonto-sites' ); ?></h3>
			<?php foreach ( $voltage_counts as $voltage ) : ?>
				<div class="vt-stat-item vt-stat-small">
					<span class="vt-stat-label"><?php echo esc_html( $voltage->voltage ); ?> V</span>
					<span class="vt-stat-value"><?php echo esc_html( $voltage->count ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php if ( $capacity_stats && $capacity_stats->avg_capacity > 0 ) : ?>
			<h3><?php esc_html_e( 'Kapazitätsbereich', 'ayonto-sites' ); ?></h3>
			<div class="vt-stat-item vt-stat-small">
				<span class="vt-stat-label"><?php esc_html_e( 'Durchschnitt', 'ayonto-sites' ); ?></span>
				<span class="vt-stat-value"><?php echo esc_html( number_format( $capacity_stats->avg_capacity, 0 ) ); ?> Ah</span>
			</div>
			<div class="vt-stat-item vt-stat-small">
				<span class="vt-stat-label"><?php esc_html_e( 'Bereich', 'ayonto-sites' ); ?></span>
				<span class="vt-stat-value"><?php echo esc_html( number_format( $capacity_stats->min_capacity, 0 ) ); ?> - <?php echo esc_html( number_format( $capacity_stats->max_capacity, 0 ) ); ?> Ah</span>
			</div>
		<?php endif; ?>
		<?php
	}
	
	/**
	 * Render quick actions widget
	 *
	 * @return void
	 */
	private function render_quick_actions() {
		?>
		<h2><?php esc_html_e( 'Schnellaktionen', 'ayonto-sites' ); ?></h2>
		
		<div class="vt-quick-actions-grid">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=vt_battery' ) ); ?>" class="vt-action-button">
				<span class="dashicons dashicons-plus"></span>
				<span class="vt-action-label"><?php esc_html_e( 'Neue Lösung', 'ayonto-sites' ); ?></span>
			</a>
			
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ayonto-import' ) ); ?>" class="vt-action-button">
				<span class="dashicons dashicons-upload"></span>
				<span class="vt-action-label"><?php esc_html_e( 'Daten importieren', 'ayonto-sites' ); ?></span>
			</a>
			
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ayonto-settings' ) ); ?>" class="vt-action-button">
				<span class="dashicons dashicons-admin-settings"></span>
				<span class="vt-action-label"><?php esc_html_e( 'Einstellungen', 'ayonto-sites' ); ?></span>
			</a>
			
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=vt_battery' ) ); ?>" class="vt-action-button">
				<span class="dashicons dashicons-list-view"></span>
				<span class="vt-action-label"><?php esc_html_e( 'Alle Lösungen', 'ayonto-sites' ); ?></span>
			</a>
		</div>
		<?php
	}
	
	/**
	 * Render recent activity widget
	 *
	 * @return void
	 */
	private function render_recent_activity() {
		$recent_posts = get_posts( array(
			'post_type'      => 'vt_battery',
			'posts_per_page' => 5,
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'post_status'    => 'publish',
		) );
		
		?>
		<h2><?php esc_html_e( 'Letzte Aktivitäten', 'ayonto-sites' ); ?></h2>
		
		<?php if ( ! empty( $recent_posts ) ) : ?>
			<ul class="vt-recent-list">
				<?php foreach ( $recent_posts as $post ) : ?>
					<li class="vt-recent-item">
						<a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>">
							<?php echo esc_html( $post->post_title ); ?>
						</a>
						<span class="vt-recent-date">
							<?php
							/* translators: %s: time difference */
							printf( esc_html__( 'Geändert %s', 'ayonto-sites' ), esc_html( human_time_diff( strtotime( $post->post_modified ), current_time( 'timestamp' ) ) ) );
							?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="vt-no-items"><?php esc_html_e( 'Noch keine Lösungen vorhanden.', 'ayonto-sites' ); ?></p>
		<?php endif; ?>
		<?php
	}
	
	/**
	 * Render system status widget
	 *
	 * @return void
	 */
	private function render_system_status() {
		global $wp_version;
		
		// Check PHP version.
		$php_version    = PHP_VERSION;
		$php_required   = '7.4';
		$php_status     = version_compare( $php_version, $php_required, '>=' );
		
		// Check WordPress version.
		$wp_required = '5.8';
		$wp_status   = version_compare( $wp_version, $wp_required, '>=' );
		
		// Check required plugins.
		$elementor_active = is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active( 'elementor-pro/elementor-pro.php' );
		$rankmath_active  = is_plugin_active( 'seo-by-rank-math/rank-math.php' );
		
		// Check permalink structure.
		$permalink_structure = get_option( 'permalink_structure' );
		$permalink_status    = ! empty( $permalink_structure );
		
		?>
		<h2><?php esc_html_e( 'System Status', 'ayonto-sites' ); ?></h2>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'Plugin Version', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo esc_html( AYONTO_SITES_VERSION ); ?></span>
			<span class="vt-status-icon vt-status-ok">✓</span>
		</div>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'WordPress', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo esc_html( $wp_version ); ?></span>
			<span class="vt-status-icon <?php echo $wp_status ? 'vt-status-ok' : 'vt-status-warning'; ?>">
				<?php echo $wp_status ? '✓' : '⚠'; ?>
			</span>
		</div>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'PHP', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo esc_html( $php_version ); ?></span>
			<span class="vt-status-icon <?php echo $php_status ? 'vt-status-ok' : 'vt-status-error'; ?>">
				<?php echo $php_status ? '✓' : '✗'; ?>
			</span>
		</div>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'Elementor', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo $elementor_active ? esc_html__( 'Aktiv', 'ayonto-sites' ) : esc_html__( 'Nicht aktiv', 'ayonto-sites' ); ?></span>
			<span class="vt-status-icon <?php echo $elementor_active ? 'vt-status-ok' : 'vt-status-warning'; ?>">
				<?php echo $elementor_active ? '✓' : '⚠'; ?>
			</span>
		</div>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'Rank Math', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo $rankmath_active ? esc_html__( 'Aktiv', 'ayonto-sites' ) : esc_html__( 'Nicht aktiv', 'ayonto-sites' ); ?></span>
			<span class="vt-status-icon <?php echo $rankmath_active ? 'vt-status-ok' : 'vt-status-warning'; ?>">
				<?php echo $rankmath_active ? '✓' : '⚠'; ?>
			</span>
		</div>
		
		<div class="vt-system-item">
			<span class="vt-system-label"><?php esc_html_e( 'Permalinks', 'ayonto-sites' ); ?></span>
			<span class="vt-system-value"><?php echo $permalink_status ? esc_html__( 'SEO-freundlich', 'ayonto-sites' ) : esc_html__( 'Standard', 'ayonto-sites' ); ?></span>
			<span class="vt-status-icon <?php echo $permalink_status ? 'vt-status-ok' : 'vt-status-warning'; ?>">
				<?php echo $permalink_status ? '✓' : '⚠'; ?>
			</span>
		</div>
		<?php
	}
	
	/**
	 * Render data debug widget (NEW!)
	 *
	 * Shows data integrity checks and potential issues.
	 *
	 * @return void
	 */
	private function render_data_debug() {
		global $wpdb;
		
		// Count batteries by status.
		$status_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_status, COUNT(*) as count
				FROM {$wpdb->posts}
				WHERE post_type = %s
				GROUP BY post_status",
				'vt_battery'
			)
		);
		
		// Count batteries without technology.
		$no_tech = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT p.ID)
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s
				WHERE p.post_type = %s
				AND p.post_status = %s
				AND (pm.meta_value IS NULL OR pm.meta_value = '')",
				'technology',
				'vt_battery',
				'publish'
			)
		);
		
		// Count batteries without brand.
		$no_brand = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT p.ID)
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s
				WHERE p.post_type = %s
				AND p.post_status = %s
				AND (pm.meta_value IS NULL OR pm.meta_value = '')",
				'brand',
				'vt_battery',
				'publish'
			)
		);
		
		// Count batteries without capacity.
		$no_capacity = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT p.ID)
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s
				WHERE p.post_type = %s
				AND p.post_status = %s
				AND (pm.meta_value IS NULL OR pm.meta_value = '' OR CAST(pm.meta_value AS DECIMAL) = 0)",
				'capacity_ah',
				'vt_battery',
				'publish'
			)
		);
		
		?>
		<h2><?php esc_html_e( 'Datenqualität', 'ayonto-sites' ); ?></h2>
		
		<?php if ( ! empty( $status_counts ) ) : ?>
			<h3><?php esc_html_e( 'Post Status', 'ayonto-sites' ); ?></h3>
			<?php foreach ( $status_counts as $status ) : ?>
				<div class="vt-debug-item">
					<span class="vt-debug-label"><?php echo esc_html( ucfirst( $status->post_status ) ); ?></span>
					<span class="vt-debug-value"><?php echo esc_html( $status->count ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<h3><?php esc_html_e( 'Fehlende Daten', 'ayonto-sites' ); ?></h3>
		
		<div class="vt-debug-item <?php echo $no_tech > 0 ? 'vt-debug-warning' : 'vt-debug-ok'; ?>">
			<span class="vt-debug-label"><?php esc_html_e( 'Ohne Technologie', 'ayonto-sites' ); ?></span>
			<span class="vt-debug-value"><?php echo esc_html( $no_tech ); ?></span>
			<span class="vt-status-icon"><?php echo $no_tech > 0 ? '⚠' : '✓'; ?></span>
		</div>
		
		<div class="vt-debug-item <?php echo $no_brand > 0 ? 'vt-debug-warning' : 'vt-debug-ok'; ?>">
			<span class="vt-debug-label"><?php esc_html_e( 'Ohne Marke', 'ayonto-sites' ); ?></span>
			<span class="vt-debug-value"><?php echo esc_html( $no_brand ); ?></span>
			<span class="vt-status-icon"><?php echo $no_brand > 0 ? '⚠' : '✓'; ?></span>
		</div>
		
		<div class="vt-debug-item <?php echo $no_capacity > 0 ? 'vt-debug-warning' : 'vt-debug-ok'; ?>">
			<span class="vt-debug-label"><?php esc_html_e( 'Ohne Kapazität', 'ayonto-sites' ); ?></span>
			<span class="vt-debug-value"><?php echo esc_html( $no_capacity ); ?></span>
			<span class="vt-status-icon"><?php echo $no_capacity > 0 ? '⚠' : '✓'; ?></span>
		</div>
		
		<?php if ( $no_tech > 0 || $no_brand > 0 || $no_capacity > 0 ) : ?>
			<p class="vt-debug-hint">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Tipp: Verwenden Sie den Datenimport, um fehlende Informationen zu ergänzen.', 'ayonto-sites' ); ?>
			</p>
		<?php endif; ?>
		<?php
	}
}
