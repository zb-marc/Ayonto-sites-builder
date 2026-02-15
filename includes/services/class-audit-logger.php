<?php
/**
 * Audit Logger for security-critical actions
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
 * Audit_Logger class
 *
 * Tracks security-critical actions for compliance and forensics
 *
 * @since 0.1.51
 */
class Audit_Logger {

	/**
	 * Log an action
	 *
	 * @since 0.1.51
	 * @param string $action Action performed.
	 * @param array  $data   Additional data.
	 * @return void
	 */
	public static function log( $action, $data = array() ) {
		$user_id = get_current_user_id();
		$user    = wp_get_current_user();

		$log_entry = array(
			'timestamp'  => current_time( 'mysql' ),
			'user_id'    => $user_id,
			'user_login' => $user->user_login,
			'action'     => sanitize_text_field( $action ),
			'ip'         => self::get_user_ip(),
			'data'       => $data,
		);

		// Get existing logs.
		$logs = get_option( 'ayonto_audit_log', array() );

		if ( ! is_array( $logs ) ) {
			$logs = array();
		}

		// Keep only last 100 entries for performance.
		if ( count( $logs ) >= 100 ) {
			array_shift( $logs );
		}

		// Add new entry.
		$logs[] = $log_entry;

		// Save (no autoload for performance).
		update_option( 'ayonto_audit_log', $logs, false );

		// Trigger action for external logging systems.
		do_action( 'ayonto_audit_log', $action, $log_entry );
	}

	/**
	 * Get user IP address
	 *
	 * @since 0.1.51
	 * @return string
	 */
	private static function get_user_ip() {
		$ip = '';

		// Check for various IP headers.
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		// Validate IP format.
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			$ip = '0.0.0.0';
		}

		return $ip;
	}

	/**
	 * Get audit logs
	 *
	 * @since 0.1.51
	 * @param int $limit Number of entries.
	 * @return array
	 */
	public static function get_logs( $limit = 50 ) {
		$logs = get_option( 'ayonto_audit_log', array() );

		if ( ! is_array( $logs ) ) {
			return array();
		}

		// Return last X entries.
		return array_slice( $logs, -$limit, $limit, true );
	}

	/**
	 * Clear audit logs
	 *
	 * @since 0.1.51
	 * @return void
	 */
	public static function clear_logs() {
		delete_option( 'ayonto_audit_log' );

		// Log the clearing itself.
		self::log( 'audit_logs_cleared' );
	}

	/**
	 * Get log statistics
	 *
	 * @since 0.1.51
	 * @return array
	 */
	public static function get_stats() {
		$logs = get_option( 'ayonto_audit_log', array() );

		if ( ! is_array( $logs ) || empty( $logs ) ) {
			return array(
				'total'           => 0,
				'unique_users'    => 0,
				'unique_actions'  => 0,
				'date_range'      => array(),
			);
		}

		$users    = array_unique( array_column( $logs, 'user_id' ) );
		$actions  = array_unique( array_column( $logs, 'action' ) );
		$first    = reset( $logs );
		$last     = end( $logs );

		return array(
			'total'           => count( $logs ),
			'unique_users'    => count( $users ),
			'unique_actions'  => count( $actions ),
			'date_range'      => array(
				'start' => isset( $first['timestamp'] ) ? $first['timestamp'] : '',
				'end'   => isset( $last['timestamp'] ) ? $last['timestamp'] : '',
			),
		);
	}
}
