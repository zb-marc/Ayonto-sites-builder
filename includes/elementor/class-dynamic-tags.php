<?php
/**
 * Elementor Dynamic Tags
 *
 * @package    Ayonto_Sites
 * @subpackage Elementor
 * @since      0.1.29
 */

namespace Ayonto\Sites\Elementor;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic_Tags class
 *
 * Registers custom Elementor Dynamic Tags for battery/solution data.
 *
 * @since 0.1.29
 */
class Dynamic_Tags {

	/**
	 * Singleton instance
	 *
	 * @var Dynamic_Tags
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Dynamic_Tags
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
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_dynamic_tags' ) );
	}

	/**
	 * Register dynamic tags
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Dynamic tags manager.
	 * @return void
	 */
	public function register_dynamic_tags( $dynamic_tags_manager ) {
		// Register tag group.
		$dynamic_tags_manager->register_group(
			'ayonto',
			array(
				'title' => __( 'Ayonto', 'ayonto-sites' ),
			)
		);

		// Register individual tags.
		$dynamic_tags_manager->register( new Additional_Content_Tag() );
	}
}

/**
 * Additional Content Dynamic Tag
 *
 * @since 0.1.29
 */
class Additional_Content_Tag extends Tag {

	/**
	 * Get tag name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'vt-additional-content';
	}

	/**
	 * Get tag title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Zusätzlicher Inhalt', 'ayonto-sites' );
	}

	/**
	 * Get tag group
	 *
	 * @return string|array
	 */
	public function get_group() {
		return 'ayonto';
	}

	/**
	 * Get tag categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	/**
	 * Render tag output
	 *
	 * @return void
	 */
	public function render() {
		$post_id = get_the_ID();

		if ( ! $post_id || 'vt_battery' !== get_post_type( $post_id ) ) {
			return;
		}

		// Get additional content from meta.
		$content = get_post_meta( $post_id, 'additional_content', true );

		if ( empty( $content ) ) {
			return;
		}

		// Apply WordPress content filters to process shortcodes, embeds, etc.
		$content = apply_filters( 'the_content', $content );

		echo $content; // Already sanitized via wp_kses_post in save.
	}
}
