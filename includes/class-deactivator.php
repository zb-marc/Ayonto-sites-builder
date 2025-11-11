<?php
/**
 * Plugin deactivation handler
 *
 * @package    Voltrana_Sites
 * @subpackage Includes
 * @since      0.1.0
 */

namespace Voltrana\Sites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivator class
 *
 * Handles plugin deactivation tasks.
 *
 * @since 0.1.0
 */
class Deactivator {

	/**
	 * Deactivate plugin
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Flush rewrite rules.
		flush_rewrite_rules();
	}
}
