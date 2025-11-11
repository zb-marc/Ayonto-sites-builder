<?php
/**
 * Cache management functionality
 *
 * @package    Voltrana_Sites
 * @subpackage Services
 * @since      0.1.0
 */

namespace Voltrana\Sites\Services;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache_Manager class
 *
 * Handles caching and cache invalidation for battery data.
 *
 * @since 0.1.0
 */
class Cache_Manager {

	/**
	 * Singleton instance
	 *
	 * @var Cache_Manager
	 */
	private static $instance = null;

	/**
	 * Cache group
	 *
	 * @var string
	 */
	private $cache_group = 'voltrana_batteries';

	/**
	 * Get singleton instance
	 *
	 * @return Cache_Manager
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
		// Invalidate cache on post save.
		add_action( 'save_post_vt_battery', array( $this, 'invalidate_cache' ) );

		// Invalidate cache on term changes.
		add_action( 'created_term', array( $this, 'invalidate_cache' ) );
		add_action( 'edited_term', array( $this, 'invalidate_cache' ) );
		add_action( 'delete_term', array( $this, 'invalidate_cache' ) );
	}

	/**
	 * Get cache key for list query
	 *
	 * @param array $args Query arguments.
	 * @return string Cache key.
	 */
	public function get_list_cache_key( $args ) {
		$args_hash = md5( wp_json_encode( $args ) );
		return "vt:list:{$args_hash}";
	}

	/**
	 * Get cache key for spec table
	 *
	 * @param int   $post_id Post ID.
	 * @param array $fields  Fields to display.
	 * @return string Cache key.
	 */
	public function get_spec_cache_key( $post_id, $fields = array() ) {
		$fields_hash = md5( wp_json_encode( $fields ) );
		return "vt:spec:{$post_id}:{$fields_hash}";
	}

	/**
	 * Invalidate all cache
	 *
	 * @return void
	 */
	public function invalidate_cache() {
		// FIXED: Use wp_cache_flush() instead of non-existent wp_cache_delete_group()
		// This will flush ALL caches, which is safer than trying to delete a specific group
		wp_cache_flush();

		// Alternative: Delete specific keys if we track them
		// For now, flush all is the safest approach
		
		/**
		 * Fires after cache invalidation
		 *
		 * @since 0.1.0
		 */
		do_action( 'voltrana_cache_invalidated' );
	}

	/**
	 * Get cached data
	 *
	 * @param string $key Cache key.
	 * @return mixed|false Cached data or false if not found.
	 */
	public function get( $key ) {
		return wp_cache_get( $key, $this->cache_group );
	}

	/**
	 * Set cached data
	 *
	 * @param string $key        Cache key.
	 * @param mixed  $data       Data to cache.
	 * @param int    $expiration Expiration time in seconds (default: 300 = 5 minutes).
	 * @return bool True on success, false on failure.
	 */
	public function set( $key, $data, $expiration = 300 ) {
		return wp_cache_set( $key, $data, $this->cache_group, $expiration );
	}

	/**
	 * Delete cached data
	 *
	 * @param string $key Cache key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $key ) {
		return wp_cache_delete( $key, $this->cache_group );
	}

	/**
	 * Check if object cache is available
	 *
	 * @return bool True if object cache is available.
	 */
	public function is_object_cache_available() {
		return wp_using_ext_object_cache();
	}

	/**
	 * Prewarm cache for landing pages
	 *
	 * @param array $landing_pages Landing page IDs.
	 * @return void
	 */
	public function prewarm_landing_pages( $landing_pages ) {
		if ( empty( $landing_pages ) ) {
			return;
		}

		foreach ( $landing_pages as $page_id ) {
			// Build default query args for this landing page.
			$args = array(
				'post_type'      => 'vt_battery',
				'posts_per_page' => 24,
				'orderby'        => 'title',
				'order'          => 'ASC',
			);

			// Get and cache the results.
			$cache_key = $this->get_list_cache_key( $args );
			$query     = new \WP_Query( $args );
			$this->set( $cache_key, $query->posts, 3600 ); // Cache for 1 hour.
		}
	}

	/**
	 * Get cache stats (if Redis/Memcached is available)
	 *
	 * @return array Cache statistics.
	 */
	public function get_stats() {
		$stats = array(
			'enabled' => $this->is_object_cache_available(),
			'type'    => 'none',
		);

		if ( $this->is_object_cache_available() ) {
			// Try to detect cache type.
			if ( class_exists( 'Redis' ) ) {
				$stats['type'] = 'redis';
			} elseif ( class_exists( 'Memcached' ) ) {
				$stats['type'] = 'memcached';
			} else {
				$stats['type'] = 'object-cache';
			}
		}

		return $stats;
	}
}
