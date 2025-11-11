<?php
/**
 * Custom post type registration
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
 * Post_Type class
 *
 * Registers custom post type and taxonomy for batteries.
 *
 * @since 0.1.0
 */
class Post_Type {

	/**
	 * Singleton instance
	 *
	 * @var Post_Type
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Post_Type
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
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_meta_fields' ) );
	}

	/**
	 * Register custom post type
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Lösungen', 'Post type general name', 'voltrana-sites' ),
			'singular_name'         => _x( 'Lösung', 'Post type singular name', 'voltrana-sites' ),
			'menu_name'             => _x( 'Lösungen', 'Admin Menu text', 'voltrana-sites' ),
			'name_admin_bar'        => _x( 'Lösung', 'Add New on Toolbar', 'voltrana-sites' ),
			'add_new'               => __( 'Neue hinzufügen', 'voltrana-sites' ),
			'add_new_item'          => __( 'Neue Lösung hinzufügen', 'voltrana-sites' ),
			'new_item'              => __( 'Neue Lösung', 'voltrana-sites' ),
			'edit_item'             => __( 'Lösung bearbeiten', 'voltrana-sites' ),
			'view_item'             => __( 'Lösung ansehen', 'voltrana-sites' ),
			'all_items'             => __( 'Alle Lösungen', 'voltrana-sites' ),
			'search_items'          => __( 'Lösungen durchsuchen', 'voltrana-sites' ),
			'parent_item_colon'     => __( 'Übergeordnete Lösung:', 'voltrana-sites' ),
			'not_found'             => __( 'Keine Lösungen gefunden.', 'voltrana-sites' ),
			'not_found_in_trash'    => __( 'Keine Lösungen im Papierkorb gefunden.', 'voltrana-sites' ),
			'featured_image'        => _x( 'Lösungs-Bild', 'Overrides the "Featured Image" phrase', 'voltrana-sites' ),
			'set_featured_image'    => _x( 'Lösungs-Bild festlegen', 'Overrides the "Set featured image" phrase', 'voltrana-sites' ),
			'remove_featured_image' => _x( 'Lösungs-Bild entfernen', 'Overrides the "Remove featured image" phrase', 'voltrana-sites' ),
			'use_featured_image'    => _x( 'Als Lösungs-Bild verwenden', 'Overrides the "Use as featured image" phrase', 'voltrana-sites' ),
			'archives'              => _x( 'Lösungs-Archive', 'The post type archive label used in nav menus', 'voltrana-sites' ),
			'insert_into_item'      => _x( 'In Lösung einfügen', 'Overrides the "Insert into post" phrase', 'voltrana-sites' ),
			'uploaded_to_this_item' => _x( 'Zu dieser Lösung hochgeladen', 'Overrides the "Uploaded to this post" phrase', 'voltrana-sites' ),
			'filter_items_list'     => _x( 'Lösungs-Liste filtern', 'Screen reader text for the filter links', 'voltrana-sites' ),
			'items_list_navigation' => _x( 'Lösungs-Listen-Navigation', 'Screen reader text for the pagination', 'voltrana-sites' ),
			'items_list'            => _x( 'Lösungs-Liste', 'Screen reader text for the items list', 'voltrana-sites' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'loesung', 'with_front' => false ),  // BUILD 011: Spezifischer Slug statt '/'
			'capability_type'    => 'post',
			'has_archive'        => false,  // Kein Archiv
			'hierarchical'       => false,  // Keine Lösung-zu-Lösung-Hierarchie
			'menu_position'      => 56,
			'menu_icon'          => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+CiAgPHJlY3QgZmlsbD0iI2ZmZmZmZiIgeD0iNSIgeT0iMiIgd2lkdGg9IjEwIiBoZWlnaHQ9IjIiLz4KICA8cmVjdCBmaWxsPSIjZmZmZmZmIiB4PSIzIiB5PSI0IiB3aWR0aD0iMTQiIGhlaWdodD0iMTQiIHJ4PSIxIi8+CiAgPHJlY3QgZmlsbD0iIzAwMDAwMCIgeD0iNSIgeT0iNiIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIi8+CiAgPHJlY3QgZmlsbD0iI2ZmZmZmZiIgeD0iNyIgeT0iOCIgd2lkdGg9IjYiIGhlaWdodD0iMiIvPgogIDxyZWN0IGZpbGw9IiNmZmZmZmYiIHg9IjciIHk9IjExIiB3aWR0aD0iNiIgaGVpZ2h0PSIyIi8+Cjwvc3ZnPgo=',
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( 'vt_battery', $args );
	}

	/**
	 * Register taxonomy
	 *
	 * IMPORTANT: Only ONE taxonomy (vt_category)!
	 * Brand, Series, Technology, Voltage are Meta Fields!
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'              => _x( 'Kategorien', 'taxonomy general name', 'voltrana-sites' ),
			'singular_name'     => _x( 'Kategorie', 'taxonomy singular name', 'voltrana-sites' ),
			'search_items'      => __( 'Kategorien durchsuchen', 'voltrana-sites' ),
			'all_items'         => __( 'Alle Kategorien', 'voltrana-sites' ),
			'parent_item'       => __( 'Übergeordnete Kategorie', 'voltrana-sites' ),
			'parent_item_colon' => __( 'Übergeordnete Kategorie:', 'voltrana-sites' ),
			'edit_item'         => __( 'Kategorie bearbeiten', 'voltrana-sites' ),
			'update_item'       => __( 'Kategorie aktualisieren', 'voltrana-sites' ),
			'add_new_item'      => __( 'Neue Kategorie hinzufügen', 'voltrana-sites' ),
			'new_item_name'     => __( 'Neuer Kategoriename', 'voltrana-sites' ),
			'menu_name'         => __( 'Kategorien', 'voltrana-sites' ),
		);

		// Build 016: Taxonomie verstecken (aber registriert lassen für später).
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => false, // Build 016: versteckt.
			'show_admin_column' => false, // Build 016: versteckt.
			'show_in_rest'      => false, // Build 016: versteckt.
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'batterien' ),
		);

		register_taxonomy( 'vt_category', array( 'vt_battery' ), $args );
	}

	/**
	 * Register meta fields
	 *
	 * IMPORTANT: Brand, Series, Technology, Voltage are Meta Fields, NOT taxonomies!
	 *
	 * @return void
	 */
	/**
	 * Register meta fields for battery post type
	 *
	 * CRITICAL FIX Build 014: Sanitization callbacks must accept 4 arguments!
	 * WordPress passes: ($value, $meta_key, $object_type, $object_subtype)
	 * But floatval() and absint() only accept 1 argument
	 * → Use wrapper callbacks that accept 4 args and pass only $value
	 *
	 * @return void
	 */
	public function register_meta_fields() {
		// String meta fields.
		$string_fields = array(
			'model'       => __( 'Modell', 'voltrana-sites' ),
			'ean'         => __( 'EAN', 'voltrana-sites' ),
			'brand'       => __( 'Marke', 'voltrana-sites' ),          // Meta Field!
			'series'      => __( 'Serie', 'voltrana-sites' ),          // Meta Field!
			'technology'  => __( 'Technologie', 'voltrana-sites' ),    // Meta Field!
			'terminals'   => __( 'Pole/Klemmen', 'voltrana-sites' ),
			'datasheet_url' => __( 'Datenblatt-URL', 'voltrana-sites' ),
			'circuit_type'     => __( 'Schaltung', 'voltrana-sites' ),       // Build 015: New!
			'product_group'    => __( 'Produktgruppe', 'voltrana-sites' ),   // Build 015: New!
			'application_area' => __( 'Anwendungsbereich', 'voltrana-sites' ), // Build 015: New!
		);

		foreach ( $string_fields as $key => $label ) {
			register_post_meta(
				'vt_battery',
				$key,
				array(
					'type'              => 'string',
					'description'       => $label,
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}

		// Numeric meta fields.
		$numeric_fields = array(
			'capacity_ah' => __( 'Kapazität (Ah)', 'voltrana-sites' ),
			'voltage_v'   => __( 'Spannung (V)', 'voltrana-sites' ),  // Meta Field!
			'cca_a'       => __( 'Kaltstartstrom (A)', 'voltrana-sites' ),
			'weight_kg'   => __( 'Gewicht (kg)', 'voltrana-sites' ),
		);

		foreach ( $numeric_fields as $key => $label ) {
			register_post_meta(
				'vt_battery',
				$key,
				array(
					'type'              => 'number',
					'description'       => $label,
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => array( $this, 'sanitize_float_meta' ),
				)
			);
		}

		// Integer meta fields.
		register_post_meta(
			'vt_battery',
			'warranty_months',
			array(
				'type'              => 'integer',
				'description'       => __( 'Garantie (Monate)', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_int_meta' ),
			)
		);
		
		// Build 034: Parent Page ID.
		register_post_meta(
			'vt_battery',
			'vt_parent_page_id',
			array(
				'type'              => 'integer',
				'description'       => __( 'Übergeordnete Seite (ID)', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_int_meta' ),
			)
		);

		// Object meta field: dimensions_mm.
		// FIXED: Complete REST API schema with items specification for nested object.
		register_post_meta(
			'vt_battery',
			'dimensions_mm',
			array(
				'type'              => 'object',
				'description'       => __( 'Abmessungen (mm)', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'l' => array(
								'type'        => 'number',
								'description' => __( 'Länge (mm)', 'voltrana-sites' ),
							),
							'w' => array(
								'type'        => 'number',
								'description' => __( 'Breite (mm)', 'voltrana-sites' ),
							),
							'h' => array(
								'type'        => 'number',
								'description' => __( 'Höhe (mm)', 'voltrana-sites' ),
							),
						),
					),
				),
				'sanitize_callback' => array( $this, 'sanitize_dimensions' ),
			)
		);

		// Array meta field: oem_refs.
		// FIXED: Complete REST API schema with items specification for array.
		register_post_meta(
			'vt_battery',
			'oem_refs',
			array(
				'type'              => 'array',
				'description'       => __( 'OEM-Referenzen', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),
				),
				'sanitize_callback' => array( $this, 'sanitize_oem_refs' ),
			)
		);

		// Array meta field: properties (Build 015: NEW!)
		// For battery properties like "Deep Cycle", "VRLA", "wartungsfrei" etc.
		register_post_meta(
			'vt_battery',
			'properties',
			array(
				'type'              => 'array',
				'description'       => __( 'Eigenschaften', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),
				),
				'sanitize_callback' => array( $this, 'sanitize_properties' ),
			)
		);

		// Build 048: Additional Content Field (WYSIWYG)
		register_post_meta(
			'vt_battery',
			'additional_content',
			array(
				'type'              => 'string',
				'description'       => __( 'Zusätzlicher Inhalt', 'voltrana-sites' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_html_content' ),
			)
		);
	}

	/**
	 * Sanitize dimensions array
	 *
	 * @param array $dimensions Dimensions data.
	 * @return array Sanitized dimensions.
	 */
	public function sanitize_dimensions( $dimensions ) {
		if ( ! is_array( $dimensions ) ) {
			return array();
		}

		$sanitized = array();

		if ( isset( $dimensions['l'] ) ) {
			$sanitized['l'] = floatval( $dimensions['l'] );
		}

		if ( isset( $dimensions['w'] ) ) {
			$sanitized['w'] = floatval( $dimensions['w'] );
		}

		if ( isset( $dimensions['h'] ) ) {
			$sanitized['h'] = floatval( $dimensions['h'] );
		}

		return $sanitized;
	}

	/**
	 * Sanitize float meta value
	 *
	 * CRITICAL: WordPress passes 4 arguments to sanitize callbacks:
	 * ($value, $meta_key, $object_type, $object_subtype)
	 * But floatval() only accepts 1 argument.
	 * This wrapper accepts all 4 args and passes only $value to floatval().
	 *
	 * @param mixed  $value         The meta value to sanitize.
	 * @param string $meta_key      The meta key (unused).
	 * @param string $object_type   The object type (unused).
	 * @param string $object_subtype The object subtype (unused).
	 * @return float Sanitized float value.
	 */
	public function sanitize_float_meta( $value, $meta_key = '', $object_type = '', $object_subtype = '' ) {
		return floatval( $value );
	}

	/**
	 * Sanitize integer meta value
	 *
	 * CRITICAL: WordPress passes 4 arguments to sanitize callbacks.
	 * This wrapper accepts all 4 args and passes only $value to absint().
	 *
	 * @param mixed  $value         The meta value to sanitize.
	 * @param string $meta_key      The meta key (unused).
	 * @param string $object_type   The object type (unused).
	 * @param string $object_subtype The object subtype (unused).
	 * @return int Sanitized integer value.
	 */
	public function sanitize_int_meta( $value, $meta_key = '', $object_type = '', $object_subtype = '' ) {
		return absint( $value );
	}

	/**
	 * Sanitize OEM references array
	 *
	 * @param array $oem_refs OEM references.
	 * @return array Sanitized OEM references.
	 */
	public function sanitize_oem_refs( $oem_refs ) {
		if ( ! is_array( $oem_refs ) ) {
			return array();
		}

		return array_map( 'sanitize_text_field', $oem_refs );
	}

	/**
	 * Sanitize properties array (Build 015)
	 *
	 * @param array $properties Battery properties.
	 * @return array Sanitized properties.
	 */
	public function sanitize_properties( $properties ) {
		if ( ! is_array( $properties ) ) {
			return array();
		}

		// Remove empty values and sanitize.
		$properties = array_filter( array_map( 'sanitize_text_field', $properties ) );

		// Remove duplicates.
		return array_values( array_unique( $properties ) );
	}

	/**
	 * Sanitize HTML content (Build 048)
	 * Allows safe HTML tags (H2-H6, P, Strong, B, Span, Tables etc.)
	 *
	 * @param string $content HTML content.
	 * @return string Sanitized HTML content.
	 */
	public function sanitize_html_content( $content ) {
		// Allowed HTML tags for additional content.
		$allowed_tags = array(
			'h2'     => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h3'     => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h4'     => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h5'     => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h6'     => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'p'      => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'span'   => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'strong' => array( 'class' => array() ),
			'b'      => array( 'class' => array() ),
			'em'     => array( 'class' => array() ),
			'i'      => array( 'class' => array() ),
			'ul'     => array( 'class' => array(), 'style' => array() ),
			'ol'     => array( 'class' => array(), 'style' => array() ),
			'li'     => array( 'class' => array(), 'style' => array() ),
			'a'      => array( 'href' => array(), 'class' => array(), 'target' => array(), 'rel' => array() ),
			'br'     => array(),
			'div'    => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			// Build 048: Table support added.
			'table'  => array( 'class' => array(), 'id' => array(), 'style' => array(), 'border' => array(), 'cellpadding' => array(), 'cellspacing' => array() ),
			'thead'  => array( 'class' => array(), 'style' => array() ),
			'tbody'  => array( 'class' => array(), 'style' => array() ),
			'tfoot'  => array( 'class' => array(), 'style' => array() ),
			'tr'     => array( 'class' => array(), 'style' => array() ),
			'th'     => array( 'class' => array(), 'style' => array(), 'colspan' => array(), 'rowspan' => array(), 'scope' => array() ),
			'td'     => array( 'class' => array(), 'style' => array(), 'colspan' => array(), 'rowspan' => array() ),
		);

		return wp_kses( $content, $allowed_tags );
	}
}
