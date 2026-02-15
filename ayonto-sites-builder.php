<?php
/**
 * Plugin Name: Ayonto Sites Builder
 * Plugin URI:  https://ayon.to
 * Description: Professional battery management system for WordPress with Elementor integration. Only ONE taxonomy (vt_category) - Brand, Series, Technology, Voltage are Meta Fields!
 * Version:     0.2.0
 * Build:       081
 * Author:      Ayonto UG
 * Author URI:  https://ayon.to
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
define( 'AYONTO_SITES_VERSION', '0.2.0' );
define( 'AYONTO_SITES_BUILD', '081' );
define( 'AYONTO_SITES_PLUGIN_FILE', __FILE__ );
define( 'AYONTO_SITES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AYONTO_SITES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AYONTO_SITES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

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
		
		// Security: Explicit namespace whitelist
		$allowed_namespaces = array(
			'Admin',
			'Frontend', 
			'Services',
			'Elementor',
			'Integrations'
		);
		
		// Extract first namespace part
		$namespace_parts = explode( '\\', $relative_class );
		$first_namespace = isset( $namespace_parts[0] ) ? $namespace_parts[0] : '';
		
		// Check if namespace is in whitelist (skip if no namespace = main classes)
		if ( ! empty( $first_namespace ) && 
		     strpos( $relative_class, '\\' ) !== false && 
		     ! in_array( $first_namespace, $allowed_namespaces, true ) ) {
			return; // Unauthorized namespace
		}
		
		// Sanitize against directory traversal attacks
		if (strpos($relative_class, '..') !== false || 
			strpos($relative_class, '/') !== false ||
			strpos($relative_class, '\\') !== false && strpos($relative_class, 'Admin\\') !== 0 && 
			strpos($relative_class, 'Frontend\\') !== 0 && strpos($relative_class, 'Services\\') !== 0 && 
			strpos($relative_class, 'Elementor\\') !== 0 && strpos($relative_class, 'Integrations\\') !== 0) {
			return; // Invalid class name, potential security issue
		}

		// Base directory.
		$base_dir = AYONTO_SITES_PLUGIN_DIR . 'includes/';

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
		require_once AYONTO_SITES_PLUGIN_DIR . 'includes/class-activator.php';
	}
	\Ayonto\Sites\Activator::activate();
}

/**
 * Plugin deactivation
 */
function ayonto_sites_deactivate() {
	// Manually load Deactivator class.
	if ( ! class_exists( 'Ayonto\Sites\Deactivator' ) ) {
		require_once AYONTO_SITES_PLUGIN_DIR . 'includes/class-deactivator.php';
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
	load_plugin_textdomain( 'ayonto-sites', false, dirname( AYONTO_SITES_PLUGIN_BASENAME ) . '/languages' );

	// Initialize components.
	\Ayonto\Sites\Post_Type::get_instance();
	\Ayonto\Sites\Shortcodes::get_instance();
	\Ayonto\Sites\Services\Cache_Manager::get_instance();
	\Ayonto\Sites\Services\Permalink_Manager::get_instance(); // NEW: Permalink Manager
	\Ayonto\Sites\Services\Data_Retention::init(); // NEW: Data Retention for GDPR (Build 070)

	// Admin components.
	if ( is_admin() ) {
		\Ayonto\Sites\Admin\Dashboard::get_instance(); // Build 061: Dashboard
		\Ayonto\Sites\Admin\Admin::get_instance();
		\Ayonto\Sites\Admin\Settings::get_instance();
		\Ayonto\Sites\Admin\Import::get_instance();
		
		// Load Parsedown for Help system.
		if ( file_exists( AYONTO_SITES_PLUGIN_DIR . 'includes/lib/parsedown.php' ) ) {
			require_once AYONTO_SITES_PLUGIN_DIR . 'includes/lib/parsedown.php';
		}
		\Ayonto\Sites\Admin\Help::get_instance(); // Build 073: Help Documentation
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
 *
 * Build 061: Dashboard als Hauptseite, konsistente Parent-Slug-Struktur
 * Build 061 FIX: Manuelle Submenu-Items für CPT um Reihenfolge zu garantieren
 */
function ayonto_sites_admin_menu() {
	// Custom Ayonto SVG icon as Data URI.
	$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMDkuMzggMTc3LjE3Ij4KICA8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMTMwLjg5LDE3Ny4wOGwtMzcuNi04NC40NS0zNy42NCw4NC41NGg3NS4yMSwwcy4wMy0uMDkuMDMtLjA5Wk01Ny42Miw1MS42N0gwbDUzLjQ0LDEyMC41NiwzNy42NS04NC41Ni05LjI3LTIwLjgyYy04LjQ3LTE1LjE4LTI0LjItMTUuMTgtMjQuMi0xNS4xOE0xMzMuMDksMTcyLjEyTDIwOS4zOCwwaC01Ny42MnMtMTcuOTEsMC0yOS4xNiwyNi4wM2wtMjcuMjIsNjEuNDEsMzcuNyw4NC42OFoiLz4KPC9zdmc+Cg==';
	
	add_menu_page(
		__( 'Ayonto', 'ayonto-sites' ),
		__( 'Ayonto', 'ayonto-sites' ),
		'manage_options',
		'ayonto-root', // Build 061: Konsistenter Parent-Slug für alle Submenüs
		array( \Ayonto\Sites\Admin\Dashboard::get_instance(), 'render_page' ), // Dashboard als Default-Page
		$icon_svg,
		56
	);
}
add_action( 'admin_menu', 'ayonto_sites_admin_menu' );

/**

/**
 * Fix admin submenu order
 * 
 * Ensures CPT submenu items appear in correct order after Dashboard
 * Adds "New Item" link if missing
 * 
 * @since 0.1.41
 */
function ayonto_sites_fix_admin_menu_order() {
	global $submenu;
	
	if ( ! isset( $submenu['ayonto-root'] ) ) {
		return;
	}
	
	// Check if "Neue Lösung hinzufügen" exists
	$add_new_exists = false;
	foreach ( $submenu['ayonto-root'] as $item ) {
		if ( strpos( $item[2], 'post-new.php?post_type=vt_battery' ) !== false ) {
			$add_new_exists = true;
			break;
		}
	}
	
	// If "Add New" doesn't exist, add it manually after "Alle Lösungen"
	if ( ! $add_new_exists ) {
		// Find position of "Alle Lösungen"
		$all_posts_position = null;
		foreach ( $submenu['ayonto-root'] as $key => $item ) {
			if ( strpos( $item[2], 'edit.php?post_type=vt_battery' ) !== false ) {
				$all_posts_position = $key;
				break;
			}
		}
		
		if ( $all_posts_position !== null ) {
			// Insert "Neue Lösung" right after "Alle Lösungen"
			$new_item = array(
				__( 'Neue Lösung', 'ayonto-sites' ),
				'edit_posts',
				'post-new.php?post_type=vt_battery'
			);
			
			// Insert at position after "Alle Lösungen"
			array_splice( $submenu['ayonto-root'], $all_posts_position + 1, 0, array( $new_item ) );
		}
	}
	
	// WordPress adds CPT items automatically, but we want to ensure Dashboard is first
	// Find Dashboard and move it to position 0 if it's not already there
	foreach ( $submenu['ayonto-root'] as $key => $item ) {
		if ( $item[2] === 'ayonto-dashboard' ) {
			// Dashboard found - ensure it's at position 0
			$dashboard_item = $submenu['ayonto-root'][ $key ];
			unset( $submenu['ayonto-root'][ $key ] );
			array_unshift( $submenu['ayonto-root'], $dashboard_item );
			break;
		}
	}
	
	// Re-index array to fix keys
	$submenu['ayonto-root'] = array_values( $submenu['ayonto-root'] );
}
add_action( 'admin_menu', 'ayonto_sites_fix_admin_menu_order', 999 );
