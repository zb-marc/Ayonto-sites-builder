<?php
/**
 * Permalink management
 *
 * @package    Ayonto_Sites
 * @subpackage Services
 * @since      0.1.0
 */

namespace Ayonto\Sites\Services;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Permalink_Manager class
 *
 * Handles custom permalink structure with parent pages.
 *
 * @since 0.1.0
 */
class Permalink_Manager {
	
	/**
	 * Singleton instance
	 *
	 * @var Permalink_Manager
	 */
	private static $instance = null;
	
	/**
	 * Get singleton instance
	 *
	 * @return Permalink_Manager
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
		// Permalink Filter
		add_filter( 'post_type_link', array( $this, 'custom_permalink' ), 10, 2 );
		
		// Rewrite Rules
		add_action( 'init', array( $this, 'add_rewrite_rules' ), 20 );
		
		// Build 039: Check if flush is needed (after rules are registered)
		add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 30 );
		
		// Build 039: Set flush flag after saving - Priority 100 (after meta is saved)
		add_action( 'save_post_vt_battery', array( $this, 'schedule_rewrite_flush' ), 100, 2 );
		
		// Query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		
		// Parse request
		add_action( 'parse_request', array( $this, 'parse_request' ) );
	}
	
	/**
	 * Custom permalink for vt_battery posts
	 *
	 * @param string  $permalink The post permalink.
	 * @param WP_Post $post      Post object.
	 * @return string Modified permalink.
	 */
	public function custom_permalink( $permalink, $post ) {
		if ( 'vt_battery' !== $post->post_type ) {
			return $permalink;
		}
		
		// Get parent page ID
		$parent_page_id = get_post_meta( $post->ID, 'vt_parent_page_id', true );
		
		if ( $parent_page_id ) {
			$parent_page = get_post( $parent_page_id );
			
			if ( $parent_page && 'publish' === $parent_page->post_status ) {
				// Build permalink with parent page slug
				$permalink = home_url( '/' . $parent_page->post_name . '/' . $post->post_name . '/' );
			}
		} else {
			// No parent: Root-level URL (e.g. /golfcarts/)
			$permalink = home_url( '/' . $post->post_name . '/' );
		}
		
		return $permalink;
	}
	
	/**
	 * Add custom rewrite rules
	 *
	 * BUILD 011: Added root-level rewrite rules for batteries without parent
	 * Gets all batteries and creates rewrite rules:
	 * - WITH parent: /parent-slug/battery-slug/
	 * - WITHOUT parent: /battery-slug/ (root-level)
	 *
	 * @return void
	 */
	public function add_rewrite_rules() {
		// Get all batteries (both with and without parents)
		$all_batteries = get_posts( array(
			'post_type'      => 'vt_battery',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		) );

		// Collect parent slugs and batteries without parents
		$parent_slugs = array();
		$root_level_slugs = array();
		
		foreach ( $all_batteries as $battery ) {
			$parent_id = get_post_meta( $battery->ID, 'vt_parent_page_id', true );
			
			if ( $parent_id ) {
				// Battery has parent
				$parent_page = get_post( $parent_id );
				if ( $parent_page && 'publish' === $parent_page->post_status ) {
					$parent_slugs[ $parent_id ] = $parent_page->post_name;
				}
			} else {
				// Battery without parent - add to root-level
				$root_level_slugs[] = $battery->post_name;
			}
		}

		// Create rewrite rules for each parent slug
		foreach ( $parent_slugs as $parent_slug ) {
			// Rewrite rule: /parent-slug/%postname%/
			add_rewrite_rule(
				'^' . $parent_slug . '/([^/]+)/?$',
				'index.php?vt_battery=$matches[1]',
				'top'
			);
			
			// Rewrite rule for pagination: /parent-slug/%postname%/page/2/
			add_rewrite_rule(
				'^' . $parent_slug . '/([^/]+)/page/?([0-9]{1,})/?$',
				'index.php?vt_battery=$matches[1]&paged=$matches[2]',
				'top'
			);
		}
		
		// BUILD 011: Add root-level rewrite rules for batteries WITHOUT parent
		// This allows URLs like /golfcarts/ instead of /loesung/golfcarts/
		// IMPORTANT: These are specific slugs, not a wildcard, so normal pages still work!
		foreach ( $root_level_slugs as $battery_slug ) {
			// Rewrite rule: /%postname%/ (root-level)
			add_rewrite_rule(
				'^' . $battery_slug . '/?$',
				'index.php?vt_battery=' . $battery_slug,
				'top'
			);
			
			// Rewrite rule for pagination: /%postname%/page/2/
			add_rewrite_rule(
				'^' . $battery_slug . '/page/?([0-9]{1,})/?$',
				'index.php?vt_battery=' . $battery_slug . '&paged=$matches[1]',
				'top'
			);
		}
		
		// Standard CPT rewrite with slug 'loesung' serves as fallback
		// Normal WordPress pages are NOT affected because we use specific slugs!
	}
	
	/**
	 * Add query vars
	 *
	 * @param array $vars Query vars.
	 * @return array Modified query vars.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'vt_battery';
		return $vars;
	}
	
	/**
	 * Parse request
	 *
	 * @param WP $wp WordPress object.
	 * @return void
	 */
	public function parse_request( $wp ) {
		if ( isset( $wp->query_vars['vt_battery'] ) ) {
			$wp->query_vars['post_type'] = 'vt_battery';
			$wp->query_vars['name']      = $wp->query_vars['vt_battery'];
		}
	}
	
	/**
	 * Schedule a rewrite flush for the next request (Build 039)
	 *
	 * Sets a flag when a battery post is saved, so that rewrite rules
	 * are flushed on the NEXT request.
	 *
	 * WordPress Best Practice: Use delete_option('rewrite_rules') instead
	 * of flush_rewrite_rules() to avoid timing issues. This forces WordPress
	 * to regenerate rules at the right time (when all CPTs are registered).
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function schedule_rewrite_flush( $post_id, $post ) {
		// Skip autosave and revisions
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		// Only for published posts
		if ( 'publish' !== $post->post_status ) {
			return;
		}
		
		// Set flag for next request
		update_option( 'vt_flush_rewrite_rules_flag', '1', false );
	}
	
	/**
	 * Flush rewrite rules if flag is set (Build 039)
	 *
	 * WordPress Best Practice: Delete rewrite_rules option instead of
	 * calling flush_rewrite_rules(). This is more reliable and avoids
	 * timing issues where rules are regenerated before CPTs are ready.
	 *
	 * @return void
	 */
	public function maybe_flush_rewrite_rules() {
		// Check if flush is needed
		$flush_needed = get_option( 'vt_flush_rewrite_rules_flag' );
		
		if ( '1' === $flush_needed ) {
			// Delete flag first to prevent infinite loops
			delete_option( 'vt_flush_rewrite_rules_flag' );
			
			// Delete rewrite_rules option - WordPress will regenerate on next request
			// This is more reliable than flush_rewrite_rules() according to WordPress docs
			delete_option( 'rewrite_rules' );
		}
	}
}
