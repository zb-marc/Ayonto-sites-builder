<?php
/**
 * Plugin activation handler
 *
 * @package    Ayonto_Sites
 * @subpackage Includes
 * @since      0.1.0
 */

namespace Ayonto\Sites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activator class
 *
 * Handles plugin activation tasks.
 *
 * @since 0.1.0
 */
class Activator {

	/**
	 * Activate plugin
	 *
	 * @return void
	 */
	public static function activate() {
		// Register post type and taxonomy for flush_rewrite_rules to work.
		self::register_cpt_and_taxonomy();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set default options.
		$version = defined( 'AYONTO_SITES_VERSION' ) ? AYONTO_SITES_VERSION : '0.1.0';
		add_option( 'ayonto_sites_version', $version );
		add_option( 'vt_retain_data_on_uninstall', true );
	}

	/**
	 * Temporarily register CPT and taxonomy for activation
	 *
	 * @return void
	 */
	private static function register_cpt_and_taxonomy() {
		// Register post type.
		register_post_type(
			'vt_battery',
			array(
				'public'      => true,
				'has_archive' => false,
				'rewrite'     => array( 'slug' => '/', 'with_front' => false ),  // Root-Level
			)
		);

		// Register taxonomy.
		register_taxonomy(
			'vt_category',
			array( 'vt_battery' ),
			array(
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'batterien' ),
			)
		);
	}
}
