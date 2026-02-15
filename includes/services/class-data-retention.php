<?php
/**
 * Data Retention Manager for GDPR/DSGVO Compliance
 *
 * @package    Ayonto_Sites
 * @subpackage Services
 * @since      0.1.51
 */

namespace Ayonto\Sites\Services;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data_Retention class
 *
 * Manages automatic cleanup of old data for GDPR compliance
 *
 * @since 0.1.51
 */
class Data_Retention {

	/**
	 * Initialize data retention
	 *
	 * @since 0.1.51
	 * @return void
	 */
	public static function init() {
		// Schedule daily cleanup if not already scheduled.
		if ( ! wp_next_scheduled( 'vt_data_retention_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'vt_data_retention_cleanup' );
		}

		// Hook cleanup function.
		add_action( 'vt_data_retention_cleanup', array( __CLASS__, 'cleanup' ) );

		// Hook deactivation to clear schedule.
		register_deactivation_hook( 
			\AYONTO_SITES_PLUGIN_FILE, 
			array( __CLASS__, 'deactivate' ) 
		);
	}

	/**
	 * Clean up old data
	 *
	 * @since 0.1.51
	 * @return void
	 */
	public static function cleanup() {
		// Clean import history older than 90 days (GDPR compliance).
		$history = get_option( 'vt_import_history', array() );

		if ( ! is_array( $history ) ) {
			return;
		}

		$retention_days = apply_filters( 'vt_import_retention_days', 90 );
		$cutoff         = time() - ( $retention_days * DAY_IN_SECONDS );

		// Filter out old entries.
		$history = array_filter(
			$history,
			function( $entry ) use ( $cutoff ) {
				return isset( $entry['timestamp'] ) && $entry['timestamp'] > $cutoff;
			}
		);

		// Update option with cleaned history.
		update_option( 'vt_import_history', $history );

		// Clean old transients.
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				AND option_value < %d",
				'_transient_timeout_vt_%',
				time()
			)
		);

		// Log cleanup action.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 
				sprintf( 
					'[Ayonto Sites] Data retention cleanup completed. Removed entries older than %d days.',
					$retention_days 
				) 
			);
		}
	}

	/**
	 * Deactivate cleanup schedule
	 *
	 * @since 0.1.51
	 * @return void
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'vt_data_retention_cleanup' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'vt_data_retention_cleanup' );
		}
	}

	/**
	 * Get retention statistics
	 *
	 * @since 0.1.51
	 * @return array
	 */
	public static function get_stats() {
		$history = get_option( 'vt_import_history', array() );

		if ( ! is_array( $history ) ) {
			return array(
				'total'  => 0,
				'oldest' => null,
				'newest' => null,
			);
		}

		$timestamps = array_column( $history, 'timestamp' );

		return array(
			'total'  => count( $history ),
			'oldest' => ! empty( $timestamps ) ? min( $timestamps ) : null,
			'newest' => ! empty( $timestamps ) ? max( $timestamps ) : null,
		);
	}
}
