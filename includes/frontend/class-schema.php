<?php
/**
 * Schema.org JSON-LD Output
 *
 * Outputs structured data for:
 * - Product pages (single vt_battery)
 * - CollectionPage + ItemList (category archives, landing pages)
 * - BreadcrumbList (all pages with breadcrumbs)
 * - Organization (all pages)
 *
 * @package    Voltrana_Sites
 * @subpackage Frontend
 * @since      0.1.23
 */

namespace Voltrana\Sites\Frontend;

use Voltrana\Sites\Admin\Settings_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schema class
 *
 * Handles Schema.org JSON-LD structured data output.
 *
 * @since 0.1.23
 */
class Schema {

	/**
	 * Singleton instance
	 *
	 * @var Schema
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Schema
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
		// Check if RankMath is active.
		if ( class_exists( 'RankMath' ) ) {
			// RankMath is active: Add Organization to RankMath's JSON-LD output.
			add_filter( 'rank_math/json_ld', array( $this, 'add_organization_to_rankmath' ), 99, 2 );
		} else {
			// RankMath not active: Output our own schema.
			if ( $this->should_output_schema() ) {
				add_action( 'wp_head', array( $this, 'output_schema' ), 10 );
			}
		}
	}

	/**
	 * Add Organization to RankMath's JSON-LD output
	 *
	 * @param array  $data   JSON-LD data from RankMath.
	 * @param object $jsonld RankMath JsonLD object.
	 * @return array Modified JSON-LD data.
	 */
	public function add_organization_to_rankmath( $data, $jsonld ) {
		// Only add if @graph exists and Organization is not already present.
		if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
			// Check if Organization already exists.
			$has_organization = false;
			foreach ( $data['@graph'] as $schema ) {
				if ( isset( $schema['@type'] ) && 'Organization' === $schema['@type'] ) {
					$has_organization = true;
					break;
				}
			}

			// Add our Organization if not present.
			if ( ! $has_organization ) {
				$data['@graph'][] = $this->get_organization_schema();
			}
		}

		return $data;
	}

	/**
	 * Check if schema should be output
	 *
	 * @return bool True if schema should be output.
	 */
	private function should_output_schema() {
		// Output in production, or if WP_DEBUG is true.
		return ( defined( 'WP_ENV' ) && 'production' === WP_ENV ) || WP_DEBUG;
	}

	/**
	 * Output all schema markup
	 *
	 * This is only used when RankMath is NOT active.
	 * When RankMath is active, we use add_organization_to_rankmath() instead.
	 *
	 * @return void
	 */
	public function output_schema() {
		$schemas = array();

		// Output Organization schema on ALL pages.
		$schemas[] = $this->get_organization_schema();

		// Page-type specific schemas.
		if ( is_singular( 'vt_battery' ) ) {
			// Single battery product page.
			$schemas[] = $this->get_product_schema();
			$schemas[] = $this->get_breadcrumb_schema();

		} elseif ( is_tax( 'vt_category' ) ) {
			// Category archive page.
			$schemas[] = $this->get_collection_page_schema();
			$schemas[] = $this->get_breadcrumb_schema();

		} elseif ( is_page() && $this->is_landing_page() ) {
			// Landing page with battery list.
			$schemas[] = $this->get_collection_page_schema();
			$schemas[] = $this->get_breadcrumb_schema();
		}

		// Output all schemas.
		if ( ! empty( $schemas ) ) {
			$schemas = array_filter( $schemas ); // Remove nulls.
			
			if ( ! empty( $schemas ) ) {
				echo '<script type="application/ld+json">' . "\n";
				echo wp_json_encode(
					array(
						'@context' => 'https://schema.org',
						'@graph'   => $schemas,
					),
					JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
				);
				echo "\n" . '</script>' . "\n";
			}
		}
	}

	/**
	 * Get Organization schema
	 *
	 * This schema is output on ALL pages to establish site-wide organization identity.
	 * All values are configurable in: Voltrana → Einstellungen → Schema.org
	 *
	 * @return array Organization schema.
	 */
	private function get_organization_schema() {
		$schema = array(
			'@type' => 'Organization',
			'@id'   => Settings_Helper::get_schema_org_url() . '/#organization',
			'name'  => Settings_Helper::get_schema_org_name(),
			'url'   => Settings_Helper::get_schema_org_url(),
		);

		// Add logo if configured.
		$logo = Settings_Helper::get_company_logo();
		if ( ! empty( $logo ) ) {
			$schema['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo,
			);
		}

		// Add description if configured.
		$description = Settings_Helper::get_schema_org_description();
		if ( ! empty( $description ) ) {
			$schema['description'] = $description;
		}

		// Add contact point if configured.
		$contact = Settings_Helper::get_schema_contact_point();
		if ( ! empty( $contact ) ) {
			$schema['contactPoint'] = $contact;
		}

		return $schema;
	}

	/**
	 * Get Product schema for single battery
	 *
	 * @return array|null Product schema or null.
	 */
	private function get_product_schema() {
		if ( ! is_singular( 'vt_battery' ) ) {
			return null;
		}

		global $post;
		if ( ! $post ) {
			return null;
		}

		// Get meta fields.
		$model       = get_post_meta( $post->ID, 'model', true );
		$ean         = get_post_meta( $post->ID, 'ean', true );
		$brand       = get_post_meta( $post->ID, 'brand', true );
		$capacity_ah = get_post_meta( $post->ID, 'capacity_ah', true );
		$voltage_v   = get_post_meta( $post->ID, 'voltage_v', true );
		$cca_a       = get_post_meta( $post->ID, 'cca_a', true );
		$weight_kg   = get_post_meta( $post->ID, 'weight_kg', true );
		$technology  = get_post_meta( $post->ID, 'technology', true );

		// Dimensions.
		$dim_l = get_post_meta( $post->ID, 'dimensions_mm.l', true );
		$dim_w = get_post_meta( $post->ID, 'dimensions_mm.w', true );
		$dim_h = get_post_meta( $post->ID, 'dimensions_mm.h', true );

		// Category.
		$categories = wp_get_post_terms( $post->ID, 'vt_category' );
		$category   = ! empty( $categories ) ? $categories[0]->name : '';

		// Build schema.
		$schema = array(
			'@type'       => 'Product',
			'@id'         => get_permalink( $post ) . '#product',
			'name'        => $model ? $model : get_the_title( $post ),
			'description' => $this->get_product_description( $post ),
			'url'         => get_permalink( $post ),
			'category'    => $category,
		);

		// Brand.
		if ( $brand ) {
			$schema['brand'] = array(
				'@type' => 'Brand',
				'name'  => sanitize_text_field( $brand ),
			);
		}

		// SKU & GTIN.
		if ( $ean ) {
			$schema['sku']    = sanitize_text_field( $ean );
			$schema['gtin13'] = sanitize_text_field( $ean );
		}

		// Image.
		if ( has_post_thumbnail( $post ) ) {
			$schema['image'] = get_the_post_thumbnail_url( $post, 'large' );
		}

		// Additional properties (technical specifications).
		$properties = array();

		if ( $capacity_ah ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Kapazität', 'voltrana-sites' ),
				'value' => sanitize_text_field( $capacity_ah ) . ' Ah',
			);
		}

		if ( $voltage_v ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Spannung', 'voltrana-sites' ),
				'value' => sanitize_text_field( $voltage_v ) . ' V',
			);
		}

		if ( $cca_a ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Kaltstartstrom', 'voltrana-sites' ),
				'value' => sanitize_text_field( $cca_a ) . ' A',
			);
		}

		if ( $dim_l && $dim_w && $dim_h ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Maße (L×B×H)', 'voltrana-sites' ),
				'value' => sprintf(
					'%s × %s × %s mm',
					sanitize_text_field( $dim_l ),
					sanitize_text_field( $dim_w ),
					sanitize_text_field( $dim_h )
				),
			);
		}

		if ( $weight_kg ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Gewicht', 'voltrana-sites' ),
				'value' => sanitize_text_field( $weight_kg ) . ' kg',
			);
		}

		if ( $technology ) {
			$properties[] = array(
				'@type' => 'PropertyValue',
				'name'  => __( 'Technologie', 'voltrana-sites' ),
				'value' => sanitize_text_field( $technology ),
			);
		}

		if ( ! empty( $properties ) ) {
			$schema['additionalProperty'] = $properties;
		}

		// NOTE: No offers/availability as per config (offers_strategy.mode = "none").

		return $schema;
	}

	/**
	 * Get CollectionPage schema for category archives and landing pages
	 *
	 * @return array|null CollectionPage schema or null.
	 */
	private function get_collection_page_schema() {
		$schema = null;

		if ( is_tax( 'vt_category' ) ) {
			// Category archive.
			$term = get_queried_object();
			if ( ! $term ) {
				return null;
			}

			$schema = array(
				'@type'      => 'CollectionPage',
				'@id'        => get_term_link( $term ) . '#collection',
				'name'       => sprintf(
					/* translators: %s: Category name */
					__( 'Batterien – %s', 'voltrana-sites' ),
					$term->name
				),
				'url'        => get_term_link( $term ),
				'isPartOf'   => array(
					'@id' => home_url( '/#website' ),
				),
				'mainEntity' => $this->get_item_list_schema( $term ),
			);

		} elseif ( is_page() && $this->is_landing_page() ) {
			// Landing page.
			global $post;
			if ( ! $post ) {
				return null;
			}

			$schema = array(
				'@type'      => 'CollectionPage',
				'@id'        => get_permalink( $post ) . '#collection',
				'name'       => get_the_title( $post ),
				'url'        => get_permalink( $post ),
				'isPartOf'   => array(
					'@id' => home_url( '/#website' ),
				),
				'mainEntity' => $this->get_item_list_schema(),
			);
		}

		return $schema;
	}

	/**
	 * Get ItemList schema for battery listings
	 *
	 * @param WP_Term|null $term Optional. Category term for filtering.
	 * @return array ItemList schema.
	 */
	private function get_item_list_schema( $term = null ) {
		// Query batteries.
		$args = array(
			'post_type'      => 'vt_battery',
			'posts_per_page' => 100, // Limit for performance.
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		// Filter by category if provided.
		if ( $term ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'vt_category',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			);
		}

		$batteries = get_posts( $args );

		// Build list items.
		$list_items = array();
		$position   = 1;

		foreach ( $batteries as $battery ) {
			$model = get_post_meta( $battery->ID, 'model', true );
			$name  = $model ? $model : $battery->post_title;

			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'item'     => array(
					'@type' => 'Product',
					'@id'   => get_permalink( $battery ) . '#product',
					'name'  => sanitize_text_field( $name ),
					'url'   => get_permalink( $battery ),
				),
			);
		}

		return array(
			'@type'           => 'ItemList',
			'itemListElement' => $list_items,
			'numberOfItems'   => count( $list_items ),
		);
	}

	/**
	 * Get BreadcrumbList schema
	 *
	 * @return array|null BreadcrumbList schema or null.
	 */
	private function get_breadcrumb_schema() {
		// Check if RankMath is active and handles breadcrumbs.
		if ( class_exists( 'RankMath' ) ) {
			// RankMath outputs its own breadcrumb schema, skip.
			return null;
		}

		$breadcrumbs = $this->get_breadcrumb_items();
		if ( empty( $breadcrumbs ) ) {
			return null;
		}

		$list_items = array();
		$position   = 1;

		foreach ( $breadcrumbs as $crumb ) {
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $crumb['name'],
				'item'     => $crumb['url'],
			);
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => get_permalink() . '#breadcrumb',
			'itemListElement' => $list_items,
		);
	}

	/**
	 * Get breadcrumb items
	 *
	 * @return array Array of breadcrumb items.
	 */
	private function get_breadcrumb_items() {
		$breadcrumbs = array();

		// Home.
		$breadcrumbs[] = array(
			'name' => __( 'Home', 'voltrana-sites' ),
			'url'  => home_url( '/' ),
		);

		if ( is_singular( 'vt_battery' ) ) {
			global $post;
			if ( ! $post ) {
				return $breadcrumbs;
			}

			// Parent page (if set).
			$parent_id = get_post_meta( $post->ID, 'vt_parent_page_id', true );
			if ( $parent_id ) {
				$parent_page = get_post( $parent_id );
				if ( $parent_page && 'publish' === $parent_page->post_status ) {
					$breadcrumbs[] = array(
						'name' => get_the_title( $parent_id ),
						'url'  => get_permalink( $parent_id ),
					);
				}
			}

			// Category.
			$categories = wp_get_post_terms( $post->ID, 'vt_category' );
			if ( ! empty( $categories ) ) {
				$breadcrumbs[] = array(
					'name' => $categories[0]->name,
					'url'  => get_term_link( $categories[0] ),
				);
			}

			// Current page.
			$breadcrumbs[] = array(
				'name' => get_the_title( $post ),
				'url'  => get_permalink( $post ),
			);

		} elseif ( is_tax( 'vt_category' ) ) {
			$term = get_queried_object();
			if ( $term ) {
				$breadcrumbs[] = array(
					'name' => $term->name,
					'url'  => get_term_link( $term ),
				);
			}

		} elseif ( is_page() ) {
			global $post;
			if ( $post ) {
				$breadcrumbs[] = array(
					'name' => get_the_title( $post ),
					'url'  => get_permalink( $post ),
				);
			}
		}

		return $breadcrumbs;
	}

	/**
	 * Check if current page is a landing page
	 *
	 * @return bool True if landing page.
	 */
	private function is_landing_page() {
		// Check if page has battery query/list.
		// This could be detected by checking for specific shortcodes or page templates.
		global $post;
		if ( ! $post ) {
			return false;
		}

		// Check for battery shortcodes in content.
		if ( has_shortcode( $post->post_content, 'vt_battery_list' ) ||
		     has_shortcode( $post->post_content, 'vt_battery_table' ) ||
		     has_shortcode( $post->post_content, 'vt_filters' ) ) {
			return true;
		}

		// Check for specific parent page ID (optional).
		// Could also check page template or meta field.

		return false;
	}

	/**
	 * Get product description for schema
	 *
	 * @param WP_Post $post Battery post.
	 * @return string Product description.
	 */
	private function get_product_description( $post ) {
		// Try excerpt first.
		if ( $post->post_excerpt ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}

		// Generate from meta fields.
		$model       = get_post_meta( $post->ID, 'model', true );
		$capacity_ah = get_post_meta( $post->ID, 'capacity_ah', true );
		$voltage_v   = get_post_meta( $post->ID, 'voltage_v', true );
		$technology  = get_post_meta( $post->ID, 'technology', true );
		$brand       = get_post_meta( $post->ID, 'brand', true );

		$categories = wp_get_post_terms( $post->ID, 'vt_category' );
		$category   = ! empty( $categories ) ? $categories[0]->name : '';

		if ( $model && $capacity_ah && $voltage_v ) {
			return sprintf(
				/* translators: 1: Model, 2: Capacity, 3: Voltage, 4: Technology, 5: Brand, 6: Category */
				__( '%1$s Batterie mit %2$s Ah Kapazität und %3$s V Spannung. Technologie: %4$s. Marke: %5$s. Ideal für %6$s.', 'voltrana-sites' ),
				$model,
				$capacity_ah,
				$voltage_v,
				$technology ? $technology : __( 'Standard', 'voltrana-sites' ),
				$brand ? $brand : Settings_Helper::get_default_brand(),
				$category ? $category : __( 'verschiedene Anwendungen', 'voltrana-sites' )
			);
		}

		// Fallback to title.
		return get_the_title( $post );
	}
}
