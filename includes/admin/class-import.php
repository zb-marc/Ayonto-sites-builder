<?php
/**
 * Import administration functionality
 *
 * @package    Voltrana_Sites
 * @subpackage Admin
 * @since      0.1.0
 */

namespace Voltrana\Sites\Admin;

use Voltrana\Sites\Services\Cache_Manager;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Import class
 *
 * Handles CSV/XLSX import functionality for batteries.
 *
 * @since 0.1.0
 */
class Import {

	/**
	 * Singleton instance
	 *
	 * @var Import
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Import
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
		add_action( 'admin_menu', array( $this, 'add_import_page' ) );
		add_action( 'admin_post_vt_import_csv', array( $this, 'handle_import' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add import page to admin menu
	 *
	 * @return void
	 */
	public function add_import_page() {
		add_submenu_page(
			'voltrana-settings',
			__( 'Datenimport', 'voltrana-sites' ),
			__( 'Datenimport', 'voltrana-sites' ),
			'manage_options',
			'voltrana-import',
			array( $this, 'render_import_page' )  // FIXED: war render_page
		);
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'voltrana_page_voltrana-import' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'voltrana-import',
			plugins_url( 'assets/js/admin.js', dirname( dirname( __FILE__ ) ) ),
			array( 'jquery' ),
			'0.1.0',
			true
		);

		wp_localize_script(
			'voltrana-import',
			'voltranaImport',
			array(
				'nonce'    => wp_create_nonce( 'vt_import_nonce' ),
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'messages' => array(
					'uploading' => __( 'Hochladen...', 'voltrana-sites' ),
					'processing' => __( 'Verarbeite...', 'voltrana-sites' ),
					'success' => __( 'Import erfolgreich!', 'voltrana-sites' ),
					'error' => __( 'Fehler beim Import', 'voltrana-sites' ),
				),
			)
		);
	}

	/**
	 * Render import page (Build 016: DISABLED - Placeholder only)
	 *
	 * @return void
	 */
	public function render_import_page() {
		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sie haben keine Berechtigung, auf diese Seite zuzugreifen.', 'voltrana-sites' ) );
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="notice notice-info">
				<p>
					<strong><?php esc_html_e( 'Import-Funktion vorübergehend deaktiviert', 'voltrana-sites' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'Die Import-Funktionalität wird nach Finalisierung der Datenstruktur wieder aktiviert.', 'voltrana-sites' ); ?>
				</p>
			</div>

			<div class="card">
				<h2><?php esc_html_e( 'Manuelle Dateneingabe', 'voltrana-sites' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: URL to create new solution */
						esc_html__( 'Bitte nutzen Sie vorerst die %s zur Dateneingabe.', 'voltrana-sites' ),
						'<a href="' . esc_url( admin_url( 'post-new.php?post_type=vt_battery' ) ) . '">' . esc_html__( 'manuelle Eingabe', 'voltrana-sites' ) . '</a>'
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}
	public function handle_import() {
		// Verify nonce.
		if ( ! isset( $_POST['vt_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vt_import_nonce'] ) ), 'vt_import_nonce' ) ) {
			wp_die( esc_html__( 'Sicherheitsüberprüfung fehlgeschlagen.', 'voltrana-sites' ) );
		}

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sie haben keine Berechtigung für diese Aktion.', 'voltrana-sites' ) );
		}

		// Check if file was uploaded.
		if ( ! isset( $_FILES['vt_import_file'] ) || UPLOAD_ERR_OK !== $_FILES['vt_import_file']['error'] ) {
			wp_die( esc_html__( 'Fehler beim Hochladen der Datei.', 'voltrana-sites' ) );
		}

		// Get parameters.
		$dry_run    = isset( $_POST['vt_dry_run'] ) && '1' === $_POST['vt_dry_run'];
		$batch_size = isset( $_POST['vt_batch_size'] ) ? absint( $_POST['vt_batch_size'] ) : 200;
		$batch_size = max( 1, min( 500, $batch_size ) );

		// Process import.
		$result = $this->process_import( $_FILES['vt_import_file'], $dry_run, $batch_size );

		// Save to history.
		if ( ! $dry_run ) {
			$this->save_import_history( $_FILES['vt_import_file']['name'], $result );
		}

		// Redirect back with result.
		$redirect_url = add_query_arg(
			array(
				'page'    => 'voltrana-import',
				'import'  => $result['success'] ? 'success' : 'error',
				'created' => $result['created'],
				'updated' => $result['updated'],
				'errors'  => $result['errors'],
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Process import file
	 *
	 * @param array $file     Uploaded file data.
	 * @param bool  $dry_run  Whether to run in dry-run mode.
	 * @param int   $batch_size Batch size.
	 * @return array Import results.
	 */
	private function process_import( $file, $dry_run = true, $batch_size = 200 ) {
		$result = array(
			'success' => false,
			'created' => 0,
			'updated' => 0,
			'errors'  => 0,
			'rows'    => 0,
		);

		// Validate MIME type for security
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);

		$allowed_types = array(
			'text/csv' => 'csv',
			'text/plain' => 'csv',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx'
		);

		if (!isset($allowed_types[$mime_type])) {
			wp_die( esc_html__( 'Ungültiger Dateityp. Nur CSV und XLSX erlaubt.', 'voltrana-sites' ) );
		}

		// Parse file based on extension.
		$file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		
		// Additional extension check for defense in depth
		if ($allowed_types[$mime_type] !== $file_extension) {
			wp_die( esc_html__( 'Dateiendung stimmt nicht mit Dateityp überein.', 'voltrana-sites' ) );
		}
		
		$rows           = array();

		if ( 'csv' === $file_extension ) {
			$rows = $this->parse_csv( $file['tmp_name'] );
		} elseif ( 'xlsx' === $file_extension ) {
			$rows = $this->parse_xlsx( $file['tmp_name'] );
		} else {
			return $result;
		}

		if ( empty( $rows ) ) {
			return $result;
		}

		$result['rows'] = count( $rows );

		// Process rows in batches.
		foreach ( array_chunk( $rows, $batch_size ) as $batch ) {
			foreach ( $batch as $row ) {
				$import_result = $this->import_battery( $row, $dry_run );

				if ( $import_result['success'] ) {
					if ( $import_result['created'] ) {
						++$result['created'];
					} else {
						++$result['updated'];
					}
				} else {
					++$result['errors'];
				}
			}
		}

		$result['success'] = ( $result['errors'] === 0 || $result['created'] > 0 || $result['updated'] > 0 );

		// Invalidate caches if not dry-run.
		if ( ! $dry_run && $result['success'] ) {
			Cache_Manager::get_instance()->invalidate_cache();
		}

		return $result;
	}

	/**
	 * Parse CSV file
	 *
	 * @param string $file_path Path to CSV file.
	 * @return array Parsed rows.
	 */
	private function parse_csv( $file_path ) {
		$rows = array();

		if ( false === ( $handle = fopen( $file_path, 'r' ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
			return $rows;
		}

		$header = fgetcsv( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fgetcsv

		while ( false !== ( $data = fgetcsv( $handle ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fgetcsv
			if ( count( $data ) === count( $header ) ) {
				$rows[] = array_combine( $header, $data );
			}
		}

		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		return $rows;
	}

	/**
	 * Parse XLSX file
	 *
	 * @param string $file_path Path to XLSX file.
	 * @return array Parsed rows.
	 */
	private function parse_xlsx( $file_path ) {
		// Simplified XLSX parsing - in production, use a library like PHPSpreadsheet.
		// For now, return empty array to avoid dependency.
		return array();
	}

	/**
	 * Import single battery
	 *
	 * @param array $row     Row data.
	 * @param bool  $dry_run Whether to run in dry-run mode.
	 * @return array Import result.
	 */
	private function import_battery( $row, $dry_run = true ) {
		$result = array(
			'success' => false,
			'created' => false,
		);

		// Validate required fields.
		if ( empty( $row['Model'] ) || empty( $row['Capacity_Ah'] ) || empty( $row['Voltage_V'] ) || empty( $row['Category'] ) ) {
			return $result;
		}

		// Normalize data.
		$normalized = $this->normalize_row( $row );

		if ( $dry_run ) {
			$result['success'] = true;
			$result['created'] = true;
			return $result;
		}

		// Check for existing battery by EAN or Model.
		$existing_id = $this->find_existing_battery( $normalized['ean'], $normalized['model'] );

		// Prepare post data.
		$post_data = array(
			'post_type'   => 'vt_battery',
			'post_title'  => $normalized['model'],
			'post_status' => 'publish',
		);

		if ( $existing_id ) {
			$post_data['ID'] = $existing_id;
		}

		// Insert or update post.
		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return $result;
		}

		$result['created'] = ! $existing_id;

		// Set taxonomy (only vt_category!).
		if ( ! empty( $normalized['category'] ) ) {
			wp_set_object_terms( $post_id, $normalized['category'], 'vt_category' );
		}

		// Set meta fields (NOT taxonomies!).
		$meta_fields = array(
			'model'         => $normalized['model'],
			'ean'           => $normalized['ean'],
			'brand'         => $normalized['brand'],        // Meta Field!
			'series'        => $normalized['series'],       // Meta Field!
			'technology'    => $normalized['technology'],   // Meta Field!
			'capacity_ah'   => $normalized['capacity_ah'],
			'voltage_v'     => $normalized['voltage_v'],    // Meta Field!
			'cca_a'         => $normalized['cca_a'],
			'weight_kg'     => $normalized['weight_kg'],
			'terminals'     => $normalized['terminals'],
			'warranty_months' => $normalized['warranty_months'],
			'circuit_type'     => $normalized['circuit_type'],    // Build 015: NEW!
			'product_group'    => $normalized['product_group'],   // Build 015: NEW!
			'application_area' => $normalized['application_area'], // Build 015: NEW!
		);

		// Dimensions as object.
		if ( ! empty( $normalized['dimensions'] ) ) {
			$meta_fields['dimensions_mm'] = $normalized['dimensions'];
		}

		// Properties as array (Build 015: NEW!).
		if ( ! empty( $normalized['properties'] ) ) {
			$meta_fields['properties'] = $normalized['properties'];
		}

		// Save all meta fields.
		foreach ( $meta_fields as $key => $value ) {
			if ( ! empty( $value ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		$result['success'] = true;

		return $result;
	}

	/**
	 * Normalize row data
	 *
	 * @param array $row Raw row data.
	 * @return array Normalized data.
	 */
	private function normalize_row( $row ) {
		// Technology normalization (Meta Field!).
		$tech_map = array(
			'Gel'     => 'GEL',
			'GEL'     => 'GEL',
			'Li-Ion'  => 'LiFePO4',
			'LiFePO4' => 'LiFePO4',
			'AGM'     => 'AGM',
			'EFB'     => 'EFB',
		);

		$technology = isset( $row['Technology'] ) && isset( $tech_map[ $row['Technology'] ] )
			? $tech_map[ $row['Technology'] ]
			: ( $row['Technology'] ?? '' );

		// Category normalization (Taxonomie!).
		$category_map = array(
			'Starterbatterien'   => 'starter',
			'Traktionsbatterien' => 'traktion',
		);

		$category = isset( $row['Category'] ) && isset( $category_map[ $row['Category'] ] )
			? $category_map[ $row['Category'] ]
			: ( $row['Category'] ?? '' );

		// Build dimensions object.
		$dimensions = array();
		if ( isset( $row['Length_mm'] ) && ! empty( $row['Length_mm'] ) ) {
			$dimensions['l'] = (float) $row['Length_mm'];
		}
		if ( isset( $row['Width_mm'] ) && ! empty( $row['Width_mm'] ) ) {
			$dimensions['w'] = (float) $row['Width_mm'];
		}
		if ( isset( $row['Height_mm'] ) && ! empty( $row['Height_mm'] ) ) {
			$dimensions['h'] = (float) $row['Height_mm'];
		}

		// Build 015: Extract properties from Art.bez.1 (product description).
		$properties = array();
		if ( ! empty( $row['Art.bez.1'] ) ) {
			$description = $row['Art.bez.1'];
			// Extract common battery properties.
			$property_patterns = array(
				'Deep Cycle',
				'VRLA',
				'wartungsfrei',
				'wartungsfreier',
				'Gel-Akku',
				'Zyklentyp',
				'Traktionsbatterie',
				'Antriebsbatterie',
				'AGM',
				'GEL',
				'Gel',
			);

			foreach ( $property_patterns as $pattern ) {
				if ( stripos( $description, $pattern ) !== false ) {
					$properties[] = $pattern;
				}
			}

			// Remove duplicates.
			$properties = array_unique( $properties );
		}

		// Build 015: Map new CSV columns.
		$circuit_type     = trim( $row['Schaltung'] ?? '' );
		$product_group    = trim( $row['Prod.grp. Bez.'] ?? '' );
		$application_area = trim( $row['War.grp. Bez.'] ?? '' );

		return array(
			'model'           => trim( $row['Model'] ?? '' ),
			'ean'             => strtoupper( trim( $row['EAN'] ?? '' ) ),
			'brand'           => trim( $row['Brand'] ?? '' ),          // Meta Field!
			'series'          => trim( $row['Series'] ?? '' ),         // Meta Field!
			'technology'      => $technology,                          // Meta Field!
			'capacity_ah'     => (float) ( $row['Capacity_Ah'] ?? 0 ),
			'voltage_v'       => (int) ( $row['Voltage_V'] ?? 0 ),     // Meta Field!
			'cca_a'           => (float) ( $row['CCA_A'] ?? 0 ),
			'dimensions'      => $dimensions,
			'weight_kg'       => (float) ( $row['Weight_kg'] ?? 0 ),
			'terminals'       => trim( $row['Terminals'] ?? '' ),
			'warranty_months' => (int) ( $row['Warranty_Months'] ?? 0 ),
			'category'        => $category,                            // Taxonomie!
			'circuit_type'     => $circuit_type,                        // Build 015: NEW!
			'product_group'    => $product_group,                       // Build 015: NEW!
			'application_area' => $application_area,                    // Build 015: NEW!
			'properties'       => $properties,                          // Build 015: NEW!
		);
	}

	/**
	 * Find existing battery by EAN or Model
	 *
	 * @param string $ean   EAN code.
	 * @param string $model Model name.
	 * @return int|false Post ID if found, false otherwise.
	 */
	private function find_existing_battery( $ean, $model ) {
		// Prefer EAN if available.
		if ( ! empty( $ean ) ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'vt_battery',
					'posts_per_page' => 1,
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => 'ean',
							'value'   => $ean,
							'compare' => '=',
						),
					),
				)
			);

			if ( $query->have_posts() ) {
				return $query->posts[0]->ID;
			}
		}

		// Fallback to Model.
		if ( ! empty( $model ) ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'vt_battery',
					'posts_per_page' => 1,
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => 'model',
							'value'   => $model,
							'compare' => '=',
						),
					),
				)
			);

			if ( $query->have_posts() ) {
				return $query->posts[0]->ID;
			}
		}

		return false;
	}

	/**
	 * Save import to history
	 *
	 * @param string $filename Import filename.
	 * @param array  $result   Import result.
	 * @return void
	 */
	private function save_import_history( $filename, $result ) {
		$history = get_option( 'vt_recent_imports', array() );

		$history[] = array(
			'date'   => current_time( 'Y-m-d H:i:s' ),
			'file'   => sanitize_file_name( $filename ),
			'rows'   => $result['rows'],
			'status' => $result['success'] ? 'success' : 'error',
		);

		// Keep only last 20 imports.
		$history = array_slice( $history, -20 );

		update_option( 'vt_recent_imports', $history );
	}
}
