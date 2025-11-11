<?php
/**
 * Uninstall Voltrana Sites Builder
 *
 * Removes all plugin data when deleted through WordPress admin
 *
 * @package Voltrana_Sites
 * @since 0.1.34
 */

// Security check
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
delete_option( 'voltrana_sites_settings' );
delete_option( 'voltrana_sites_version' );

// Delete all vt_battery posts
$batteries = get_posts( array(
	'post_type'   => 'vt_battery',
	'numberposts' => -1,
	'post_status' => 'any',
	'fields'      => 'ids'
) );

foreach ( $batteries as $battery_id ) {
	wp_delete_post( $battery_id, true );
}

// Delete all vt_category terms
$terms = get_terms( array(
	'taxonomy'   => 'vt_category',
	'hide_empty' => false,
	'fields'     => 'ids'
) );

foreach ( $terms as $term_id ) {
	wp_delete_term( $term_id, 'vt_category' );
}

// Clear object cache
wp_cache_flush();

// Clear any transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_voltrana_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_voltrana_%'" );

// Clear any custom tables (if any exist in future versions)
// Reserved for future use

// Log uninstall completion
error_log( 'Voltrana Sites Builder: Uninstall completed successfully' );
