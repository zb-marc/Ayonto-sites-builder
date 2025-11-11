<?php
/**
 * Rank Math SEO integration
 *
 * @package    Voltrana_Sites
 * @subpackage Integrations
 * @since      0.1.0
 */

namespace Voltrana\Sites\Integrations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rank_Math class
 *
 * Integrates with Rank Math SEO plugin.
 *
 * BUILD 040 IMPORTANT CHANGE:
 * - RankMath uses its OWN manually entered Title & Description fields
 * - Plugin DOES NOT override these fields anymore
 * - Plugin only manages:
 *   1. Breadcrumbs (with parent page support via vt_parent_page_id)
 *   2. Canonical URLs (with parent page support)
 * - Meta fields (brand, capacity_ah, etc.) are used ONLY for Schema.org JSON-LD
 *
 * @since 0.1.0
 */
class Rank_Math {

	/**
	 * Singleton instance
	 *
	 * @var Rank_Math
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Rank_Math
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
	 * BUILD 040 IMPORTANT CHANGE:
	 * - Title/Description filters REMOVED!
	 * - RankMath uses its OWN manually entered fields
	 * - Plugin only manages Breadcrumbs & Canonical URLs
	 * - Meta fields used ONLY for Schema.org JSON-LD
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Check if Rank Math is active.
		if ( ! $this->is_rank_math_active() ) {
			return;
		}

		// Breadcrumbs (with parent page support).
		add_filter( 'rank_math/frontend/breadcrumb/items', array( $this, 'filter_breadcrumbs' ), 10, 1 );

		// Canonical URL (with parent page support).
		add_filter( 'rank_math/frontend/canonical', array( $this, 'filter_canonical' ), 10, 1 );
	}

	/**
	 * Check if Rank Math is active
	 *
	 * @return bool True if Rank Math is active.
	 */
	private function is_rank_math_active() {
		return class_exists( 'RankMath' );
	}

	/**
	 * Filter breadcrumbs
	 *
	 * BUILD 013: FIXED - Inserts parent page instead of replacing
	 * 
	 * Problem in Build 012:
	 * - Tried to REPLACE CPT archive ("Lösungen")
	 * - But Rank Math shows TAXONOMY ("Golfcarts") instead
	 * - Parent page was never inserted because nothing was replaced
	 * 
	 * Solution in Build 013:
	 * - INSERT parent page AFTER Home (index 1)
	 * - Keep all other breadcrumbs (taxonomy, post title)
	 * - Result: Home → Parent Page → Taxonomy → Post Title
	 *
	 * @param array $crumbs Breadcrumb items.
	 * @return array Filtered breadcrumbs.
	 */
	public function filter_breadcrumbs( $crumbs ) {
		global $post;
		
		if ( ! is_singular( 'vt_battery' ) || ! $post ) {
			return $crumbs;
		}

		$parent_id = get_post_meta( $post->ID, 'vt_parent_page_id', true );
		
		// No parent page = no changes to breadcrumbs
		if ( ! $parent_id ) {
			return $crumbs;
		}

		$parent_page = get_post( $parent_id );
		if ( ! $parent_page || 'publish' !== $parent_page->post_status ) {
			return $crumbs;
		}

		// Create parent page breadcrumb (NUMERIC array format!)
		$parent_crumb = array(
			$parent_page->post_title,
			get_permalink( $parent_id ),
		);

		// INSERT parent page AFTER Home (position 1)
		$new_crumbs = array();
		foreach ( $crumbs as $index => $crumb ) {
			$new_crumbs[] = $crumb; // Add current breadcrumb
			
			// After Home (index 0), insert parent page
			if ( 0 === $index ) {
				$new_crumbs[] = $parent_crumb;
			}
		}

		return $new_crumbs;
	}

	/**
	 * Filter canonical URL
	 *
	 * @param string $canonical Original canonical URL.
	 * @return string Filtered canonical URL.
	 */
	public function filter_canonical( $canonical ) {
		if ( ! is_singular( 'vt_battery' ) ) {
			return $canonical;
		}

		$post = get_post();
		if ( ! $post ) {
			return $canonical;
		}

		// Ensure canonical URL for battery posts.
		return get_permalink( $post );
	}
}
