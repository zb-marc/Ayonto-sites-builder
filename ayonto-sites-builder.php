<?php
/**
 * Plugin Name: Ayonto Sites Builder
 * Plugin URI:  https://ayon.to
 * Description: Professional battery management system for WordPress with Elementor integration. Only ONE taxonomy (vt_category) - Brand, Series, Technology, Voltage are Meta Fields!
 * Version:     0.1.37
 * Build:       057
 * Author:      Marc Mirschel
 * Author URI:  https://mirschel.biz
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ayonto-sites
 * Domain Path: /languages
 *
 * @package Ayonto_Sites
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'VOLTRANA_SITES_VERSION', '0.1.37' );
define( 'VOLTRANA_SITES_BUILD', '057' );
define( 'VOLTRANA_SITES_PLUGIN_FILE', __FILE__ );
define( 'VOLTRANA_SITES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VOLTRANA_SITES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VOLTRANA_SITES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * PSR-4 Autoloader (Simplified & Fixed)
 *
 * @param string $class Class name to load.
 * @return void
 */
spl_autoload_register(
	function ( $class ) {
		// Check if class belongs to our namespace.
		$prefix = 'Ayonto\\Sites\\';
		if ( strpos( $class, $prefix ) !== 0 ) {
			return;
		}

		// Remove namespace prefix.
		$relative_class = substr( $class, strlen( $prefix ) );
		
		// Sanitize against directory traversal attacks
		if (strpos($relative_class, '..') !== false || 
			strpos($relative_class, '/') !== false ||
			strpos($relative_class, '\\') !== false && strpos($relative_class, 'Admin\\') !== 0 && 
			strpos($relative_class, 'Frontend\\') !== 0 && strpos($relative_class, 'Services\\') !== 0 && 
			strpos($relative_class, 'Elementor\\') !== 0 && strpos($relative_class, 'Integrations\\') !== 0) {
			return; // Invalid class name, potential security issue
		}

		// Base directory.
		$base_dir = VOLTRANA_SITES_PLUGIN_DIR . 'includes/';

		// Convert class name to filename: Post_Type -> post-type, Cache_Manager -> cache-manager.
		$class_name = str_replace( '_', '-', $relative_class );
		$class_name = preg_replace('/[^a-z0-9\-]/', '', strtolower( $class_name ) );

		// Handle subdirectories.
		$file = '';
		if ( strpos( $relative_class, 'Admin\\' ) === 0 ) {
			$clean_name = str_replace( 'Admin\\', '', $relative_class );
			$clean_name = str_replace( '_', '-', $clean_name );
			$clean_name = strtolower( $clean_name );
			$file       = $base_dir . 'admin/class-' . $clean_name . '.php';
		} elseif ( strpos( $relative_class, 'Frontend\\' ) === 0 ) {
			$clean_name = str_replace( 'Frontend\\', '', $relative_class );
			$clean_name = str_replace( '_', '-', $clean_name );
			$clean_name = strtolower( $clean_name );
			$file       = $base_dir . 'frontend/class-' . $clean_name . '.php';
		} elseif ( strpos( $relative_class, 'Services\\' ) === 0 ) {
			$clean_name = str_replace( 'Services\\', '', $relative_class );
			$clean_name = str_replace( '_', '-', $clean_name );
			$clean_name = strtolower( $clean_name );
			$file       = $base_dir . 'services/class-' . $clean_name . '.php';
		} elseif ( strpos( $relative_class, 'Elementor\\' ) === 0 ) {
			$clean_name = str_replace( 'Elementor\\', '', $relative_class );
			$clean_name = str_replace( '_', '-', $clean_name );
			$clean_name = strtolower( $clean_name );
			$file       = $base_dir . 'elementor/class-' . $clean_name . '.php';
		} elseif ( strpos( $relative_class, 'Integrations\\' ) === 0 ) {
			$clean_name = str_replace( 'Integrations\\', '', $relative_class );
			$clean_name = str_replace( '_', '-', $clean_name );
			$clean_name = strtolower( $clean_name );
			$file       = $base_dir . 'integrations/class-' . $clean_name . '.php';
		} else {
			// Root level classes.
			$file = $base_dir . 'class-' . $class_name . '.php';
		}

		// Load file if it exists.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

/**
 * Plugin activation
 */
function ayonto_sites_activate() {
	// Manually load Activator class.
	if ( ! class_exists( 'Ayonto\Sites\Activator' ) ) {
		require_once VOLTRANA_SITES_PLUGIN_DIR . 'includes/class-activator.php';
	}
	\Ayonto\Sites\Activator::activate();
}

/**
 * Plugin deactivation
 */
function ayonto_sites_deactivate() {
	// Manually load Deactivator class.
	if ( ! class_exists( 'Ayonto\Sites\Deactivator' ) ) {
		require_once VOLTRANA_SITES_PLUGIN_DIR . 'includes/class-deactivator.php';
	}
	\Ayonto\Sites\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ayonto_sites_activate' );
register_deactivation_hook( __FILE__, 'ayonto_sites_deactivate' );

/**
 * Initialize plugin
 */
function ayonto_sites_init() {
	// Load text domain.
	load_plugin_textdomain( 'ayonto-sites', false, dirname( VOLTRANA_SITES_PLUGIN_BASENAME ) . '/languages' );

	// Initialize components.
	\Ayonto\Sites\Post_Type::get_instance();
	\Ayonto\Sites\Shortcodes::get_instance();
	\Ayonto\Sites\Services\Cache_Manager::get_instance();
	\Ayonto\Sites\Services\Permalink_Manager::get_instance(); // NEW: Permalink Manager

	// Admin components.
	if ( is_admin() ) {
		\Ayonto\Sites\Admin\Admin::get_instance();
		\Ayonto\Sites\Admin\Settings::get_instance();
		\Ayonto\Sites\Admin\Import::get_instance();
	}

	// Frontend components.
	if ( ! is_admin() ) {
		\Ayonto\Sites\Frontend\Frontend::get_instance();
		\Ayonto\Sites\Frontend\Schema::get_instance();
	}

	// Integrations.
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		\Ayonto\Sites\Elementor\Integration::get_instance();
	}

	if ( class_exists( 'RankMath' ) ) {
		\Ayonto\Sites\Integrations\Rank_Math::get_instance();
		\Ayonto\Sites\Integrations\RankMath_Schema_Sync::get_instance(); // Build 042: Schema Sync
	}

	/**
	 * Fires after Ayonto Sites Builder has been initialized
	 *
	 * @since 0.1.0
	 */
	do_action( 'ayonto_sites_init' );
}
add_action( 'plugins_loaded', 'ayonto_sites_init' );

/**
 * Privacy Policy Integration
 * 
 * @since 0.1.34
 */
add_action( 'admin_init', function() {
	wp_add_privacy_policy_content(
		'Ayonto Sites Builder',
		wp_kses_post( 
			'<h2>' . __( 'Ayonto Sites Builder', 'ayonto-sites' ) . '</h2>' .
			'<p>' . __( 'Dieses Plugin speichert folgende Daten:', 'ayonto-sites' ) . '</p>' .
			'<ul>' .
			'<li>' . __( 'Technische Batteriedaten (Modell, Kapazität, Spannung)', 'ayonto-sites' ) . '</li>' .
			'<li>' . __( 'Import-Historie (Dateinamen und Zeitstempel)', 'ayonto-sites' ) . '</li>' .
			'<li>' . __( 'Plugin-Einstellungen (Farben, Firmenname)', 'ayonto-sites' ) . '</li>' .
			'</ul>' .
			'<p>' . __( 'Es werden keine personenbezogenen Daten erhoben oder an Dritte übertragen.', 'ayonto-sites' ) . '</p>'
		)
	);
});

/**
 * Admin menu
 */
function ayonto_sites_admin_menu() {
	// Custom Ayonto SVG icon as Data URI.
	$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMDkuMzggMTc3LjE3Ij4KICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMTMwLjg5LDE3Ny4wOGwtMzcuNi04NC40NS0zNy42NCw4NC41NGg3NS4yMSwwcy4wMy0uMDkuMDMtLjA5Wk01Ny42Miw1MS42N0gwbDUzLjQ0LDEyMC41NiwzNy42NS04NC41Ni05LjI3LTIwLjgyYy04LjQ3LTE1LjE4LTI0LjItMTUuMTgtMjQuMi0xNS4xOE0xMzMuMDksMTcyLjEyTDIwOS4zOCwwaC01Ny42MnMtMTcuOTEsMC0yOS4xNiwyNi4wM2wtMjcuMjIsNjEuNDEsMzcuNyw4NC42OFoiLz4KPC9zdmc+Cg==';
	
	add_menu_page(
		__( 'Ayonto', 'ayonto-sites' ),
		__( 'Ayonto', 'ayonto-sites' ),
		'manage_options',
		'ayonto-settings', // Zeigt direkt auf Settings (Standard-WordPress-Praxis)
		'', // Callback ist leer, weil Settings-Page den Content liefert
		$icon_svg,
		56
	);
}
add_action( 'admin_menu', 'ayonto_sites_admin_menu' );
