<?php
/**
 * Shortcodes handler
 *
 * @package    Ayonto_Sites
 * @subpackage Includes
 * @since      0.1.0
 */

namespace Ayonto\Sites;

use Ayonto\Sites\Admin\Settings_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes class
 *
 * Registers and handles plugin shortcodes.
 *
 * @since 0.1.0
 */
class Shortcodes {

	/**
	 * Singleton instance
	 *
	 * @var Shortcodes
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Shortcodes
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
		add_shortcode( 'vt_battery_list', array( $this, 'battery_list' ) );
		add_shortcode( 'vt_battery_filters', array( $this, 'battery_filters' ) );
		add_shortcode( 'vt_battery_specs', array( $this, 'battery_specs' ) );
		add_shortcode( 'vt_battery_table', array( $this, 'battery_table' ) ); // Build 015: NEW!
		add_shortcode( 'vt_additional_content', array( $this, 'additional_content' ) ); // Build 048: NEW!
		
		// Prevent wpautop from adding extra paragraphs around our shortcodes (Build 079)
		add_filter( 'the_content', array( $this, 'protect_shortcodes_from_wpautop' ), 9 );
	}

	/**
	 * Protect shortcodes from wpautop
	 * 
	 * Prevents WordPress from adding <p> and <br> tags around our shortcodes
	 * which can cause unwanted spacing issues.
	 * 
	 * @since 0.1.60
	 * @param string $content The post content.
	 * @return string Modified content.
	 */
	public function protect_shortcodes_from_wpautop( $content ) {
		// List of our shortcodes that should be protected
		$shortcodes = array(
			'vt_battery_table',
			'vt_battery_list',
			'vt_battery_filters',
			'vt_battery_specs',
			'vt_additional_content'
		);
		
		// First, temporarily replace shortcodes with placeholders
		$placeholders = array();
		foreach ( $shortcodes as $shortcode ) {
			// Match shortcode with any attributes
			$pattern = '/(\[' . $shortcode . '(?:\s[^\]]*)?(?:\](?:.*?\[\/'. $shortcode . '\])?|\]))/s';
			
			if ( preg_match_all( $pattern, $content, $matches ) ) {
				foreach ( $matches[0] as $index => $match ) {
					$placeholder = '<!--VT_SHORTCODE_' . strtoupper( $shortcode ) . '_' . $index . '-->';
					$placeholders[ $placeholder ] = $match;
					$content = str_replace( $match, $placeholder, $content );
				}
			}
		}
		
		// Let wpautop process the content (it will run after this filter)
		// But our shortcodes are now protected as HTML comments
		
		// After wpautop runs (priority 10), we need to restore our shortcodes
		// We'll add another filter for that
		add_filter( 'the_content', function( $content ) use ( $placeholders ) {
			// Restore shortcodes from placeholders
			foreach ( $placeholders as $placeholder => $shortcode ) {
				$content = str_replace( $placeholder, $shortcode, $content );
			}
			
			// Clean up any <p> or <br> tags that might have been added around our shortcodes
			$content = preg_replace( '/<p>\s*(\[vt_[^\]]+\])\s*<\/p>/', '$1', $content );
			$content = preg_replace( '/<br\s*\/?>\s*(\[vt_[^\]]+\])/', '$1', $content );
			$content = preg_replace( '/(\[vt_[^\]]+\])\s*<br\s*\/?>/', '$1', $content );
			
			return $content;
		}, 11 ); // Priority 11, after wpautop
		
		return $content;
	}

	/**
	 * Battery list shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function battery_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'limit'    => 24,
				'order'    => 'ASC',
				'category' => '',
			),
			$atts,
			'vt_battery_list'
		);

		// Basic implementation - will be enhanced in future builds.
		ob_start();
		?>
		<div class="vt-battery-list">
			<p><?php esc_html_e( 'Batterie-Liste (wird in zukünftigen Builds erweitert)', 'ayonto-sites' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Battery filters shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function battery_filters( $atts ) {
		$atts = shortcode_atts(
			array(
				'show_counts' => true,
				'style'       => 'default',
			),
			$atts,
			'vt_battery_filters'
		);

		// Basic implementation - will be enhanced in future builds.
		ob_start();
		?>
		<div class="vt-filters">
			<p><?php esc_html_e( 'Filter (wird in zukünftigen Builds erweitert)', 'ayonto-sites' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Battery specs shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function battery_specs( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => get_the_ID(),
			),
			$atts,
			'vt_battery_specs'
		);

		$post_id = absint( $atts['id'] );

		if ( ! $post_id || 'vt_battery' !== get_post_type( $post_id ) ) {
			return '';
		}

		// Basic implementation - will be enhanced in future builds.
		ob_start();
		?>
		<div class="vt-specs">
			<p><?php esc_html_e( 'Spezifikationstabelle (wird in zukünftigen Builds erweitert)', 'ayonto-sites' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Battery table shortcode (Build 016: REFACTORED!)
	 *
	 * Displays batteries from the current solution (lösung) in a responsive table format.
	 * Automatically detects the current post (solution) and loads batteries from post meta.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function battery_table( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'      => get_the_ID(), // Auto-detect current solution.
				'columns' => 'model,ean,technology,capacity_ah,voltage_v,dimensions_mm,weight_kg,product_image', // Build 055: product_image statt datasheet_url, properties entfernt.
				'orderby' => 'capacity_ah', // model, capacity_ah, voltage_v, etc.
				'order'   => 'ASC',   // ASC/DESC.
			),
			$atts,
			'vt_battery_table'
		);

		$post_id = absint( $atts['id'] );
		if ( ! $post_id || 'vt_battery' !== get_post_type( $post_id ) ) {
			return '<p class="vt-no-results">' . esc_html__( 'Ungültige Lösung.', 'ayonto-sites' ) . '</p>';
		}

		// Load batteries from post meta.
		$batteries = get_post_meta( $post_id, 'vt_batteries', true );
		if ( ! is_array( $batteries ) || empty( $batteries ) ) {
			return '<p class="vt-no-results">' . esc_html__( 'Keine Batterien gefunden.', 'ayonto-sites' ) . '</p>';
		}

		// Sort batteries.
		$batteries = $this->sort_batteries( $batteries, $atts['orderby'], $atts['order'] );

		// Enqueue styles and scripts (Build 055: added GLightbox).
		wp_enqueue_style( 'ayonto-frontend' );
		wp_enqueue_style( 'ayonto-glightbox' );
		wp_enqueue_script( 'ayonto-glightbox' );
		wp_enqueue_script( 'ayonto-glightbox-init' );

		// Parse columns.
		$columns = $this->parse_table_columns( $atts['columns'] );

		// Render table.
		ob_start();
		$this->render_battery_table_from_array( $batteries, $columns );
		$output = ob_get_clean();
		
		// Remove any trailing whitespace that could cause gaps
		return trim( $output );
	}

	/**
	 * Parse table columns from shortcode attribute
	 *
	 * @param string $columns_str Comma-separated column keys.
	 * @return array Associative array of column_key => label.
	 */
	private function parse_table_columns( $columns_str ) {
		$available_columns = array(
			'model'           => __( 'Modell', 'ayonto-sites' ),
			'brand'           => __( 'Marke', 'ayonto-sites' ),
			'series'          => __( 'Serie', 'ayonto-sites' ),
			'category'        => __( 'Kategorie', 'ayonto-sites' ),
			'technology'      => __( 'Technologie', 'ayonto-sites' ),
			'ean'             => __( 'EAN', 'ayonto-sites' ),
			'capacity_ah'     => __( 'Kapazität (Ah)', 'ayonto-sites' ),
			'voltage_v'       => __( 'Spannung (V)', 'ayonto-sites' ),
			'cca_a'           => __( 'Kaltstartstrom (A)', 'ayonto-sites' ),
			'dimensions_mm'   => __( 'Maße (mm)', 'ayonto-sites' ),
			'weight_kg'       => __( 'Gewicht (kg)', 'ayonto-sites' ),
			'terminals'       => __( 'Pole/Klemmen', 'ayonto-sites' ),
			'circuit_type'    => __( 'Schaltung', 'ayonto-sites' ),
			'product_group'   => __( 'Produktgruppe', 'ayonto-sites' ),
			'application_area' => __( 'Anwendungsbereich', 'ayonto-sites' ),
			'properties'      => __( 'Eigenschaften', 'ayonto-sites' ),
			'warranty_months' => __( 'Garantie (Monate)', 'ayonto-sites' ),
			'product_image'   => __( 'Bild', 'ayonto-sites' ), // Build 055: NEW!
			'datasheet_url'   => __( 'Datenblatt', 'ayonto-sites' ),
		);

		// Parse requested columns.
		$requested = array_map( 'trim', explode( ',', $columns_str ) );
		$columns   = array();

		foreach ( $requested as $key ) {
			if ( isset( $available_columns[ $key ] ) ) {
				$columns[ $key ] = $available_columns[ $key ];
			}
		}

		return $columns;
	}

	/**
	 * Sort batteries array (Build 016)
	 *
	 * @param array  $batteries Batteries array.
	 * @param string $orderby   Field to sort by.
	 * @param string $order     ASC or DESC.
	 * @return array Sorted batteries.
	 */
	private function sort_batteries( $batteries, $orderby, $order ) {
		$order = strtoupper( $order ) === 'DESC' ? SORT_DESC : SORT_ASC;

		// Sort by field.
		usort(
			$batteries,
			function ( $a, $b ) use ( $orderby, $order ) {
				$val_a = isset( $a[ $orderby ] ) ? $a[ $orderby ] : '';
				$val_b = isset( $b[ $orderby ] ) ? $b[ $orderby ] : '';

				// Numeric comparison for numeric fields.
				if ( in_array( $orderby, array( 'capacity_ah', 'voltage_v', 'weight_kg', 'cca_a' ), true ) ) {
					$val_a = floatval( $val_a );
					$val_b = floatval( $val_b );
				}

				if ( $val_a === $val_b ) {
					return 0;
				}

				$result = $val_a < $val_b ? -1 : 1;
				return SORT_DESC === $order ? -$result : $result;
			}
		);

		return $batteries;
	}

	/**
	 * Render battery table from array (Build 016)
	 *
	 * @param array $batteries Batteries array.
	 * @param array $columns   Columns to display.
	 * @return void
	 */
	private function render_battery_table_from_array( $batteries, $columns ) {
		?><div class="vt-battery-table-wrapper vt-style-table">
			<table class="vt-battery-table">
				<thead>
					<tr>
						<?php foreach ( $columns as $key => $label ) : ?>
							<th data-column="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $label ); ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $batteries as $battery ) : ?>
						<?php $this->render_battery_row_from_array( $battery, $columns ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div><?php
	}

	/**
	 * Render single battery row from array (Build 016)
	 *
	 * @param array $battery Battery data.
	 * @param array $columns Columns to display.
	 * @return void
	 */
	private function render_battery_row_from_array( $battery, $columns ) {
		?>
		<tr>
			<?php foreach ( $columns as $key => $label ) : ?>
				<td data-label="<?php echo esc_attr( $label ); ?>">
					<?php echo wp_kses_post( $this->get_column_value_from_array( $battery, $key ) ); ?>
				</td>
			<?php endforeach; ?>
		</tr>
		<?php
	}

	/**
	 * Get column value from battery array (Build 016)
	 *
	 * @param array  $battery Battery data.
	 * @param string $key     Column key.
	 * @return string Column value HTML.
	 */
	private function get_column_value_from_array( $battery, $key ) {
		switch ( $key ) {
			case 'model':
				$model = $battery['model'] ?? '';
				$brand = $battery['brand'] ?? Settings_Helper::get_default_brand();
				
				if ( empty( $model ) ) {
					return '—';
				}
				
				// Add brand prefix if not already present.
				$display = $model;
				if ( stripos( $model, $brand ) === false ) {
					$display = $brand . ' ' . $model;
				}
				
				return '<strong class="vt-model-name">' . esc_html( $display ) . '</strong>';

			case 'technology':
				$tech = $battery['technology'] ?? '';
				if ( ! empty( $tech ) ) {
					// Technology as badge with proper class handling.
					$tech_lower = strtolower( $tech );
					// Map for special cases with umlauts.
					$class_map = array(
						'blei-säure' => 'blei-saure',
						'säure'      => 'saure',
					);
					$tech_class = isset( $class_map[ $tech_lower ] ) ? $class_map[ $tech_lower ] : sanitize_html_class( $tech_lower );
					
					return '<span class="vt-tech-badge vt-tech-' . esc_attr( $tech_class ) . '">' . esc_html( $tech ) . '</span>';
				}
				return '—';

			case 'ean':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					return '<span class="vt-value-ean">' . esc_html( $value ) . '</span>';
				}
				return '—';

			case 'dimensions_mm':
				$l = ! empty( $battery['dimensions_l'] ) ? floatval( $battery['dimensions_l'] ) : 0;
				$w = ! empty( $battery['dimensions_w'] ) ? floatval( $battery['dimensions_w'] ) : 0;
				$h = ! empty( $battery['dimensions_h'] ) ? floatval( $battery['dimensions_h'] ) : 0;
				if ( $l && $w && $h ) {
					return sprintf( '<span class="vt-dimensions">%d × %d × %d</span>', $l, $w, $h );
				}
				return '—';

			case 'properties':
				$properties = $battery['properties'] ?? array();
				if ( ! empty( $properties ) && is_array( $properties ) ) {
					$tags = array();
					foreach ( $properties as $prop ) {
						$tags[] = '<span class="vt-property-tag">' . esc_html( $prop ) . '</span>';
					}
					return '<div class="vt-properties-list">' . implode( ' ', $tags ) . '</div>';
				}
				return '—';

			case 'datasheet_url':
				$url = $battery['datasheet_url'] ?? '';
				if ( ! empty( $url ) ) {
					// PDF Icon via CSS (SVG Background).
					return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="vt-datasheet-link" title="' . esc_attr__( 'Datenblatt öffnen', 'ayonto-sites' ) . '"><span class="vt-pdf-icon"></span></a>';
				}
				return '—';

			case 'product_image':
				// Build 055: Neues Feld für Produktbild (ersetzt datasheet_url im Standard).
				$image_id = $battery['product_image_id'] ?? 0;
				
				if ( ! empty( $image_id ) && is_numeric( $image_id ) ) {
					$image_url = wp_get_attachment_image_url( $image_id, 'medium' );
					$image_full_url = wp_get_attachment_image_url( $image_id, 'full' );
					
					if ( $image_url && $image_full_url ) {
						// Lightbox-ready Link (GLightbox compatible).
						return '<a href="' . esc_url( $image_full_url ) . '" class="glightbox vt-product-image-link" data-gallery="battery-' . esc_attr( $battery['model'] ?? '' ) . '"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $battery['model'] ?? '' ) . '" class="vt-product-image-thumb" /></a>';
					}
				}
				
				// Fallback: Icon wenn kein Bild vorhanden.
				return '<span class="vt-no-image-icon" title="' . esc_attr__( 'Kein Bild verfügbar', 'ayonto-sites' ) . '">📷</span>';


			case 'capacity_ah':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					$num = floatval( $value );
					// No decimals if it's a whole number.
					$formatted = ( $num == floor( $num ) ) ? number_format_i18n( $num, 0 ) : number_format_i18n( $num, 1 );
					return '<span class="vt-value-capacity">' . esc_html( $formatted . ' Ah' ) . '</span>';
				}
				return '—';

			case 'voltage_v':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					return '<span class="vt-value-voltage">' . esc_html( absint( $value ) . ' V' ) . '</span>';
				}
				return '—';

			case 'cca_a':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					return '<span class="vt-value-cca">' . esc_html( number_format_i18n( floatval( $value ), 0 ) . ' A' ) . '</span>';
				}
				return '—';

			case 'weight_kg':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					return '<span class="vt-value-weight">' . esc_html( number_format_i18n( floatval( $value ), 2 ) . ' kg' ) . '</span>';
				}
				return '—';

			case 'warranty_months':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					$months = absint( $value );
					// Show years if 12, 24, 36, etc.
					if ( $months >= 12 && $months % 12 === 0 ) {
						$years = $months / 12;
						return '<span class="vt-value-warranty">' . esc_html( $years . ' ' . ( $years === 1 ? __( 'Jahr', 'ayonto-sites' ) : __( 'Jahre', 'ayonto-sites' ) ) ) . '</span>';
					}
					return '<span class="vt-value-warranty">' . esc_html( $months . ' ' . __( 'Monate', 'ayonto-sites' ) ) . '</span>';
				}
				return '—';

			case 'circuit_type':
				$value = $battery[ $key ] ?? '';
				if ( ! empty( $value ) ) {
					// Circuit type mapping for better display.
					$display_map = array(
						'0'        => '0',
						'1'        => '1',
						'diagonal' => 'Diagonal',
						'serie'    => 'Serie',
						'parallel' => 'Parallel',
					);
					$display = isset( $display_map[ $value ] ) ? $display_map[ $value ] : $value;
					return '<span class="vt-value-circuit">' . esc_html( $display ) . '</span>';
				}
				return '—';

			case 'terminals':
				$value = $battery[ $key ] ?? '';
				return ! empty( $value ) ? '<span class="vt-value-terminals">' . esc_html( $value ) . '</span>' : '—';

			case 'series':
				$value = $battery[ $key ] ?? '';
				return ! empty( $value ) ? '<span class="vt-value-series">' . esc_html( $value ) . '</span>' : '—';

			case 'brand':
				$value = $battery[ $key ] ?? Settings_Helper::get_default_brand();
				return '<span class="vt-value-brand">' . esc_html( $value ) . '</span>';

			default:
				$value = $battery[ $key ] ?? '';
				return ! empty( $value ) ? esc_html( $value ) : '—';
		}
	}

	/**
	 * Additional content shortcode (Build 048)
	 * Outputs the formatted additional content field
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode output.
	 */
	public function additional_content( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => get_the_ID(),
				'class' => 'vt-additional-content',
			),
			$atts,
			'vt_additional_content'
		);

		$post_id = absint( $atts['id'] );

		if ( ! $post_id || 'vt_battery' !== get_post_type( $post_id ) ) {
			return '';
		}

		// Get additional content from meta.
		$content = get_post_meta( $post_id, 'additional_content', true );

		if ( empty( $content ) ) {
			return '';
		}

		// Apply WordPress content filters to process shortcodes, embeds, etc.
		$content = apply_filters( 'the_content', $content );

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( $atts['class'] ),
			$content
		);
	}
}

