<?php
/**
 * RankMath Schema Sync
 *
 * Synchronisiert Batterien aus "Batterien für diese Lösung" Metabox
 * automatisch in RankMath's Schema Generator.
 *
 * @package    Voltrana_Sites
 * @subpackage Integrations
 * @since      0.1.25
 */

namespace Voltrana\Sites\Integrations;

use Voltrana\Sites\Admin\Settings_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RankMath_Schema_Sync class
 *
 * Synchronisiert Batterie-Daten aus Metabox in RankMath Schema.
 *
 * @since 0.1.25
 */
class RankMath_Schema_Sync {

	/**
	 * Singleton instance
	 *
	 * @var RankMath_Schema_Sync
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return RankMath_Schema_Sync
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
		// Check if RankMath is active.
		if ( ! class_exists( 'RankMath' ) ) {
			return;
		}

		// Sync batteries to RankMath on post save.
		add_action( 'save_post_vt_battery', array( $this, 'sync_batteries_to_rankmath' ), 20, 1 );

		// Filter RankMath JSON-LD output to add ItemList.
		// Priorität 99 = läuft NACH RankMath's eigenen Filtern.
		add_filter( 'rank_math/json_ld', array( $this, 'add_itemlist_to_schema' ), 99, 2 );

		// Admin notice after sync.
		add_action( 'admin_notices', array( $this, 'show_sync_notice' ) );
	}

	/**
	 * Show admin notice after sync
	 *
	 * @return void
	 */
	public function show_sync_notice() {
		global $post, $pagenow;

		if ( 'post.php' !== $pagenow || ! $post || 'vt_battery' !== $post->post_type ) {
			return;
		}

		// Check if batteries were just saved.
		if ( ! isset( $_GET['message'] ) || '1' !== $_GET['message'] ) {
			return;
		}

		$batteries = get_post_meta( $post->ID, 'vt_batteries', true );
		if ( empty( $batteries ) || ! is_array( $batteries ) ) {
			return;
		}

		$count = count( $batteries );

		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<strong><?php _e( 'Schema.org synchronisiert:', 'voltrana-sites' ); ?></strong>
				<?php
				printf(
					/* translators: %d: Number of batteries */
					_n(
						'%d Batterie wurde automatisch in RankMath Schema Generator übertragen.',
						'%d Batterien wurden automatisch in RankMath Schema Generator übertragen.',
						$count,
						'voltrana-sites'
					),
					$count
				);
				?>
			</p>
			<p style="font-size: 12px; color: #646970;">
				<?php _e( 'Die Daten werden als ItemList im Frontend ausgegeben. Prüfe die Ausgabe mit Google Rich Results Test.', 'voltrana-sites' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Sync batteries from metabox to RankMath Schema
	 *
	 * Wird aufgerufen wenn Post gespeichert wird.
	 * WICHTIG: Speichert NICHT in Meta, nur Filter-basiert!
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function sync_batteries_to_rankmath( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['vt_batteries_nonce'] ) || 
		     ! wp_verify_nonce( $_POST['vt_batteries_nonce'], 'vt_batteries_nonce' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get batteries from metabox.
		$batteries = isset( $_POST['vt_batteries'] ) ? $_POST['vt_batteries'] : array();

		// WICHTIG: Lösche ALLE RankMath Schema Metas die ItemList enthalten könnten.
		// Das verhindert Duplikate und Fehler.
		delete_post_meta( $post_id, 'rank_math_schema_ItemList' );
		delete_post_meta( $post_id, 'rank_math_schema_Product' );
		
		// Auch mögliche custom schemas löschen.
		$all_metas = get_post_meta( $post_id );
		foreach ( $all_metas as $key => $value ) {
			if ( strpos( $key, 'rank_math_schema_' ) === 0 ) {
				delete_post_meta( $post_id, $key );
			}
		}

		// NICHT in Meta speichern - nur für Admin Notice zählen.
		// Das Schema wird zur Laufzeit via Filter eingefügt.

		// Action für Logging/Debug.
		do_action( 'voltrana_batteries_synced_to_rankmath', $post_id, count( $batteries ) );
	}

	/**
	 * Add ItemList schema to RankMath JSON-LD output
	 *
	 * @param array  $data Current schema data.
	 * @param object $jsonld RankMath JSON-LD object.
	 * @return array Modified schema data.
	 */
	public function add_itemlist_to_schema( $data, $jsonld ) {
		// Nur auf vt_battery Posts.
		if ( ! is_singular( 'vt_battery' ) ) {
			return $data;
		}

		global $post;
		if ( ! $post ) {
			return $data;
		}

		// Get batteries from metabox.
		$batteries = get_post_meta( $post->ID, 'vt_batteries', true );

		if ( empty( $batteries ) || ! is_array( $batteries ) ) {
			return $data;
		}

		// Build ItemList.
		$itemlist = $this->build_itemlist_schema( $batteries, $post->ID );

		// Add to @graph.
		if ( ! isset( $data['@graph'] ) ) {
			$data['@graph'] = array();
		}

		// WICHTIG: Entferne ALLE fehlerhaften ItemList-Schemas.
		// RankMath könnte aus alten Metas fehlerhafte Schemas geladen haben.
		$cleaned_graph = array();
		
		foreach ( $data['@graph'] as $schema ) {
			// Skip wenn:
			
			// 1. Schema ist ein ARRAY (nicht Objekt) - FEHLER!
			if ( isset( $schema[0] ) ) {
				continue; // Array überspringen
			}
			
			// 2. Schema hat verschachteltes "schema" Property (FALSCH!)
			if ( isset( $schema['schema'] ) ) {
				continue;
			}
			
			// 3. Schema hat "itemlist" Property (FALSCH!)
			if ( isset( $schema['itemlist'] ) ) {
				continue;
			}
			
			// 4. Schema ist ItemList mit unserem #batterylist ID
			if ( isset( $schema['@type'] ) && 'ItemList' === $schema['@type'] ) {
				if ( isset( $schema['@id'] ) && false !== strpos( $schema['@id'], '#batterylist' ) ) {
					continue;
				}
			}
			
			// 5. Schema hat KEIN @type (FEHLER!)
			if ( ! isset( $schema['@type'] ) ) {
				continue;
			}
			
			// Schema ist OK - behalten
			$cleaned_graph[] = $schema;
		}
		
		$data['@graph'] = $cleaned_graph;

		// Add ItemList as separate entity in @graph (NUR EINMAL!).
		$data['@graph'][] = $itemlist;

		return $data;
	}

	/**
	 * Build ItemList schema from batteries
	 *
	 * @param array $batteries Array of battery data.
	 * @param int   $post_id   Post ID.
	 * @return array ItemList schema.
	 */
	private function build_itemlist_schema( $batteries, $post_id ) {
		$list_items = array();
		$position   = 1;

		foreach ( $batteries as $battery ) {
			// Skip if no model.
			if ( empty( $battery['model'] ) ) {
				continue;
			}

			// Build Product item.
			$product = array(
				'@type' => 'Product',
				'name'  => sanitize_text_field( $battery['model'] ),
			);

			// Brand (immer Voltrana).
			if ( ! empty( $battery['brand'] ) ) {
				$product['brand'] = array(
					'@type' => 'Brand',
					'name'  => sanitize_text_field( $battery['brand'] ),
				);
			}

			// SKU & GTIN.
			if ( ! empty( $battery['ean'] ) ) {
				$product['sku']    = sanitize_text_field( $battery['ean'] );
				$product['gtin13'] = sanitize_text_field( $battery['ean'] );
			}

			// Description aus Meta Fields generieren.
			$product['description'] = $this->generate_product_description( $battery );

			// additionalProperty mit technischen Daten.
			$properties = array();

			if ( ! empty( $battery['technology'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Technologie', 'voltrana-sites' ),
					'value' => sanitize_text_field( $battery['technology'] ),
				);
			}

			if ( ! empty( $battery['capacity_ah'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Kapazität', 'voltrana-sites' ),
					'value' => sanitize_text_field( $battery['capacity_ah'] ) . ' Ah',
				);
			}

			if ( ! empty( $battery['voltage_v'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Spannung', 'voltrana-sites' ),
					'value' => sanitize_text_field( $battery['voltage_v'] ) . ' V',
				);
			}

			if ( ! empty( $battery['cca_a'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Kaltstartstrom', 'voltrana-sites' ),
					'value' => sanitize_text_field( $battery['cca_a'] ) . ' A',
				);
			}

			// Maße.
			if ( ! empty( $battery['dimensions_l'] ) && 
			     ! empty( $battery['dimensions_w'] ) && 
			     ! empty( $battery['dimensions_h'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Maße (L×B×H)', 'voltrana-sites' ),
					'value' => sprintf(
						'%s × %s × %s mm',
						sanitize_text_field( $battery['dimensions_l'] ),
						sanitize_text_field( $battery['dimensions_w'] ),
						sanitize_text_field( $battery['dimensions_h'] )
					),
				);
			}

			if ( ! empty( $battery['weight_kg'] ) ) {
				$properties[] = array(
					'@type' => 'PropertyValue',
					'name'  => __( 'Gewicht', 'voltrana-sites' ),
					'value' => sanitize_text_field( $battery['weight_kg'] ) . ' kg',
				);
			}

			if ( ! empty( $properties ) ) {
				$product['additionalProperty'] = $properties;
			}

			// URL zum Datenblatt (falls vorhanden).
			if ( ! empty( $battery['datasheet_url'] ) ) {
				$product['url'] = esc_url( $battery['datasheet_url'] );
			}

			// Add to ListItem.
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'item'     => $product,
			);
		}

		// Build ItemList.
		return array(
			'@type'           => 'ItemList',
			'@id'             => get_permalink( $post_id ) . '#batterylist',
			'name'            => sprintf(
				/* translators: %s: Post title */
				__( 'Batterien für %s', 'voltrana-sites' ),
				get_the_title( $post_id )
			),
			'itemListElement' => $list_items,
			'numberOfItems'   => count( $list_items ),
		);
	}

	/**
	 * Generate product description from battery data
	 *
	 * @param array $battery Battery data.
	 * @return string Product description.
	 */
	private function generate_product_description( $battery ) {
		$model       = ! empty( $battery['model'] ) ? $battery['model'] : '';
		$technology  = ! empty( $battery['technology'] ) ? $battery['technology'] : '';
		$capacity_ah = ! empty( $battery['capacity_ah'] ) ? $battery['capacity_ah'] : '';
		$voltage_v   = ! empty( $battery['voltage_v'] ) ? $battery['voltage_v'] : '';
		$brand       = ! empty( $battery['brand'] ) ? $battery['brand'] : Settings_Helper::get_default_brand();

		if ( $model && $capacity_ah && $voltage_v ) {
			return sprintf(
				/* translators: 1: Model, 2: Capacity, 3: Voltage, 4: Technology, 5: Brand */
				__( '%1$s Batterie mit %2$s Ah Kapazität und %3$s V Spannung. Technologie: %4$s. Marke: %5$s.', 'voltrana-sites' ),
				$model,
				$capacity_ah,
				$voltage_v,
				$technology ? $technology : __( 'Standard', 'voltrana-sites' ),
				$brand
			);
		}

		return sprintf(
			/* translators: %s: Model */
			__( '%s Batterie', 'voltrana-sites' ),
			$model ? $model : __( 'Unbekannt', 'voltrana-sites' )
		);
	}
}
