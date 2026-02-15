<?php
/**
 * Plugin Settings
 *
 * Manages all plugin settings with tabbed interface:
 * - General (Company, Brand, URLs)
 * - Schema.org (Organization data)
 * - Design (Colors, Styling)
 * - Import (Import defaults)
 * - Frontend (Display options)
 *
 * @package    Ayonto_Sites
 * @subpackage Admin
 * @since      0.1.28
 */

namespace Ayonto\Sites\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class
 *
 * Handles all plugin settings with WordPress Settings API.
 *
 * @since 0.1.28
 */
class Settings {

	/**
	 * Singleton instance
	 *
	 * @var Settings
	 */
	private static $instance = null;

	/**
	 * Settings option name
	 *
	 * @var string
	 */
	const OPTION_NAME = 'ayonto_sites_settings';

	/**
	 * Active tab
	 *
	 * @var string
	 */
	private $active_tab = 'general';

	/**
	 * Get singleton instance
	 *
	 * @return Settings
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
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_submenu_page(
			'ayonto-root',
			__( 'Einstellungen', 'ayonto-sites' ),
			__( 'Einstellungen', 'ayonto-sites' ),
			'manage_options',
			'ayonto-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'ayonto_page_ayonto-settings' !== $hook ) {
			return;
		}

		// Enqueue WordPress media uploader.
		wp_enqueue_media();

		// Enqueue color picker.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Enqueue Ayonto Admin CSS (unified design system).
		wp_enqueue_style(
			'ayonto-admin',
			AYONTO_SITES_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			AYONTO_SITES_VERSION
		);

		// Enqueue Settings Enhancements JS (NEW!).
		wp_enqueue_script(
			'ayonto-settings',
			AYONTO_SITES_PLUGIN_URL . 'assets/js/settings.js',
			array( 'jquery', 'wp-color-picker' ),
			AYONTO_SITES_VERSION,
			true
		);

		// Custom settings script (inline for media uploader).
		wp_add_inline_script(
			'ayonto-settings',
			'
			jQuery(document).ready(function($) {
				// Color picker.
				$(".ayonto-color-picker").wpColorPicker();
				
				// Media uploader.
				$(".ayonto-upload-button").click(function(e) {
					e.preventDefault();
					var button = $(this);
					var input = button.prev("input");
					
					var customUploader = wp.media({
						title: "Logo auswählen",
						button: { text: "Logo verwenden" },
						multiple: false
					});
					
					customUploader.on("select", function() {
						var attachment = customUploader.state().get("selection").first().toJSON();
						input.val(attachment.url);
						input.trigger("change");
					});
					
					customUploader.open();
				});
			});
			'
		);
	}

	/**
	 * Register all settings
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register main option.
		register_setting(
			'ayonto_sites_settings_group',
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_default_settings(),
			)
		);

		// Register sections and fields.
		$this->register_general_settings();
		$this->register_schema_settings();
		$this->register_design_settings();
		$this->register_import_settings();
		$this->register_frontend_settings();
	}

	/**
	 * Register general settings section
	 *
	 * @return void
	 */
	private function register_general_settings() {
		add_settings_section(
			'ayonto_general_section',
			__( 'Allgemeine Einstellungen', 'ayonto-sites' ),
			array( $this, 'render_general_section_description' ),
			'ayonto-settings-general'
		);

		// Company Name.
		add_settings_field(
			'company_name',
			__( 'Firmenname', 'ayonto-sites' ),
			array( $this, 'render_text_field' ),
			'ayonto-settings-general',
			'ayonto_general_section',
			array(
				'label_for'   => 'company_name',
				'description' => __( 'Der Name Ihres Unternehmens (z.B. "Ayonto")', 'ayonto-sites' ),
				'placeholder' => 'Ayonto',
			)
		);

		// Company URL.
		add_settings_field(
			'company_url',
			__( 'Firmen-URL', 'ayonto-sites' ),
			array( $this, 'render_url_field' ),
			'ayonto-settings-general',
			'ayonto_general_section',
			array(
				'label_for'   => 'company_url',
				'description' => __( 'Die Haupt-URL Ihrer Website', 'ayonto-sites' ),
				'placeholder' => 'https://ayon.to',
			)
		);

		// Default Brand.
		add_settings_field(
			'default_brand',
			__( 'Standard-Marke', 'ayonto-sites' ),
			array( $this, 'render_text_field' ),
			'ayonto-settings-general',
			'ayonto_general_section',
			array(
				'label_for'   => 'default_brand',
				'description' => __( 'Standard-Marke für Batterien ohne Markenangabe', 'ayonto-sites' ),
				'placeholder' => 'Ayonto',
			)
		);

		// Company Logo URL.
		add_settings_field(
			'company_logo',
			__( 'Firmen-Logo URL', 'ayonto-sites' ),
			array( $this, 'render_upload_field' ),
			'ayonto-settings-general',
			'ayonto_general_section',
			array(
				'label_for'   => 'company_logo',
				'description' => __( 'Logo für Schema.org und andere Ausgaben', 'ayonto-sites' ),
			)
		);
	}

	/**
	 * Register schema settings section
	 *
	 * @return void
	 */
	private function register_schema_settings() {
		add_settings_section(
			'ayonto_schema_section',
			__( 'Schema.org Organisation', 'ayonto-sites' ),
			array( $this, 'render_schema_section_description' ),
			'ayonto-settings-schema'
		);

		// Organization Name.
		add_settings_field(
			'schema_org_name',
			__( 'Organisationsname', 'ayonto-sites' ),
			array( $this, 'render_text_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_org_name',
				'description' => __( 'Name für Schema.org Organization (falls abweichend vom Firmennamen)', 'ayonto-sites' ),
				'placeholder' => __( 'Wird von Firmennamen übernommen', 'ayonto-sites' ),
			)
		);

		// Organization URL.
		add_settings_field(
			'schema_org_url',
			__( 'Organisations-URL', 'ayonto-sites' ),
			array( $this, 'render_url_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_org_url',
				'description' => __( 'URL für Schema.org Organization (falls abweichend von Firmen-URL)', 'ayonto-sites' ),
				'placeholder' => __( 'Wird von Firmen-URL übernommen', 'ayonto-sites' ),
			)
		);

		// Organization Description.
		add_settings_field(
			'schema_org_description',
			__( 'Organisationsbeschreibung', 'ayonto-sites' ),
			array( $this, 'render_textarea_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_org_description',
				'description' => __( 'Kurze Beschreibung Ihres Unternehmens für Schema.org', 'ayonto-sites' ),
				'rows'        => 3,
			)
		);

		// Contact Type.
		add_settings_field(
			'schema_contact_type',
			__( 'Kontakttyp', 'ayonto-sites' ),
			array( $this, 'render_select_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_contact_type',
				'description' => __( 'Art des Kontakts für Schema.org ContactPoint', 'ayonto-sites' ),
				'options'     => array(
					''                   => __( '– Keine Angabe –', 'ayonto-sites' ),
					'customer service'   => __( 'Kundenservice', 'ayonto-sites' ),
					'technical support'  => __( 'Technischer Support', 'ayonto-sites' ),
					'sales'              => __( 'Vertrieb', 'ayonto-sites' ),
					'billing support'    => __( 'Abrechnungssupport', 'ayonto-sites' ),
					'bill payment'       => __( 'Rechnungszahlung', 'ayonto-sites' ),
				),
			)
		);

		// Contact Telephone.
		add_settings_field(
			'schema_contact_telephone',
			__( 'Kontakt-Telefon', 'ayonto-sites' ),
			array( $this, 'render_text_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_contact_telephone',
				'description' => __( 'Telefonnummer für Schema.org ContactPoint (z.B. "+49 30 1234567")', 'ayonto-sites' ),
				'placeholder' => '+49 30 1234567',
			)
		);

		// Contact Email.
		add_settings_field(
			'schema_contact_email',
			__( 'Kontakt-E-Mail', 'ayonto-sites' ),
			array( $this, 'render_email_field' ),
			'ayonto-settings-schema',
			'ayonto_schema_section',
			array(
				'label_for'   => 'schema_contact_email',
				'description' => __( 'E-Mail-Adresse für Schema.org ContactPoint', 'ayonto-sites' ),
				'placeholder' => 'info@ayon.to',
			)
		);
	}

	/**
	 * Register design settings section
	 *
	 * @return void
	 */
	private function register_design_settings() {
		add_settings_section(
			'ayonto_design_section',
			__( 'Design & Farben', 'ayonto-sites' ),
			array( $this, 'render_design_section_description' ),
			'ayonto-settings-design'
		);

		// Primary Color.
		add_settings_field(
			'primary_color',
			__( 'Primärfarbe', 'ayonto-sites' ),
			array( $this, 'render_color_field' ),
			'ayonto-settings-design',
			'ayonto_design_section',
			array(
				'label_for'   => 'primary_color',
				'description' => __( 'Hauptfarbe für Buttons und Akzente', 'ayonto-sites' ),
				'default'     => '#004B61',
			)
		);

		// Secondary Color.
		add_settings_field(
			'secondary_color',
			__( 'Sekundärfarbe', 'ayonto-sites' ),
			array( $this, 'render_color_field' ),
			'ayonto-settings-design',
			'ayonto_design_section',
			array(
				'label_for'   => 'secondary_color',
				'description' => __( 'Zweite Farbe für Hintergründe und Hervorhebungen', 'ayonto-sites' ),
				'default'     => '#F0F4F5',
			)
		);

		// Accent Color.
		add_settings_field(
			'accent_color',
			__( 'Akzentfarbe', 'ayonto-sites' ),
			array( $this, 'render_color_field' ),
			'ayonto-settings-design',
			'ayonto_design_section',
			array(
				'label_for'   => 'accent_color',
				'description' => __( 'Farbe für wichtige Elemente (z.B. Call-to-Action, Hover-Effekte)', 'ayonto-sites' ),
				'default'     => '#F79D00',
			)
		);

		// Border Color.
		add_settings_field(
			'border_color',
			__( 'Rahmenfarbe', 'ayonto-sites' ),
			array( $this, 'render_color_field' ),
			'ayonto-settings-design',
			'ayonto_design_section',
			array(
				'label_for'   => 'border_color',
				'description' => __( 'Farbe für Rahmen und Trennlinien', 'ayonto-sites' ),
				'default'     => '#e5e7eb',
			)
		);
	}

	/**
	 * Register import settings section
	 *
	 * @return void
	 */
	private function register_import_settings() {
		add_settings_section(
			'ayonto_import_section',
			__( 'Import-Einstellungen', 'ayonto-sites' ),
			array( $this, 'render_import_section_description' ),
			'ayonto-settings-import'
		);

		// Auto-assign Brand.
		add_settings_field(
			'import_auto_brand',
			__( 'Marke automatisch setzen', 'ayonto-sites' ),
			array( $this, 'render_checkbox_field' ),
			'ayonto-settings-import',
			'ayonto_import_section',
			array(
				'label_for'   => 'import_auto_brand',
				'description' => __( 'Wenn aktiviert, wird die Standard-Marke automatisch gesetzt, wenn keine Marke angegeben ist', 'ayonto-sites' ),
				'default'     => true,
			)
		);

		// Default Batch Size.
		add_settings_field(
			'import_batch_size',
			__( 'Batch-Größe', 'ayonto-sites' ),
			array( $this, 'render_number_field' ),
			'ayonto-settings-import',
			'ayonto_import_section',
			array(
				'label_for'   => 'import_batch_size',
				'description' => __( 'Anzahl der Datensätze pro Import-Batch (Standard: 200)', 'ayonto-sites' ),
				'default'     => 200,
				'min'         => 10,
				'max'         => 500,
			)
		);

		// Max File Size.
		add_settings_field(
			'import_max_file_size',
			__( 'Maximale Dateigröße (MB)', 'ayonto-sites' ),
			array( $this, 'render_number_field' ),
			'ayonto-settings-import',
			'ayonto_import_section',
			array(
				'label_for'   => 'import_max_file_size',
				'description' => __( 'Maximale Größe für Import-Dateien in MB (Standard: 10)', 'ayonto-sites' ),
				'default'     => 10,
				'min'         => 1,
				'max'         => 50,
			)
		);
	}

	/**
	 * Register frontend settings section
	 *
	 * @return void
	 */
	private function register_frontend_settings() {
		add_settings_section(
			'ayonto_frontend_section',
			__( 'Frontend-Optionen', 'ayonto-sites' ),
			array( $this, 'render_frontend_section_description' ),
			'ayonto-settings-frontend'
		);

		// Auto-inject Specs.
		add_settings_field(
			'auto_inject_specs',
			__( 'Spezifikationen automatisch einfügen', 'ayonto-sites' ),
			array( $this, 'render_checkbox_field' ),
			'ayonto-settings-frontend',
			'ayonto_frontend_section',
			array(
				'label_for'   => 'auto_inject_specs',
				'description' => __( 'Wenn aktiviert, werden Spezifikationstabellen automatisch in Batterie-Seiten eingefügt', 'ayonto-sites' ),
				'default'     => false,
			)
		);

		// Spec Table Style.
		add_settings_field(
			'spec_table_style',
			__( 'Tabellentyp', 'ayonto-sites' ),
			array( $this, 'render_select_field' ),
			'ayonto-settings-frontend',
			'ayonto_frontend_section',
			array(
				'label_for'   => 'spec_table_style',
				'description' => __( 'Stil der Spezifikationstabelle', 'ayonto-sites' ),
				'options'     => array(
					'default'   => __( 'Standard', 'ayonto-sites' ),
					'compact'   => __( 'Kompakt', 'ayonto-sites' ),
					'detailed'  => __( 'Detailliert', 'ayonto-sites' ),
					'minimal'   => __( 'Minimal', 'ayonto-sites' ),
				),
				'default'     => 'default',
			)
		);

		// Show Icons in Tables.
		add_settings_field(
			'show_icons',
			__( 'Icons in Tabellen anzeigen', 'ayonto-sites' ),
			array( $this, 'render_checkbox_field' ),
			'ayonto-settings-frontend',
			'ayonto_frontend_section',
			array(
				'label_for'   => 'show_icons',
				'description' => __( 'Zeigt Icons neben Spezifikationen an', 'ayonto-sites' ),
				'default'     => false,
			)
		);
	}

	/**
	 * Get default settings
	 *
	 * @return array Default settings.
	 */
	private function get_default_settings() {
		return array(
			// General.
			'company_name'            => 'Ayonto',
			'company_url'             => home_url( '/' ),
			'default_brand'           => 'Ayonto',
			'company_logo'            => '',

			// Schema.
			'schema_org_name'         => '',
			'schema_org_url'          => '',
			'schema_org_description'  => '',
			'schema_contact_type'     => 'customer service',
			'schema_contact_telephone' => '',
			'schema_contact_email'    => '',

			// Design - Ayonto Brand Colors from frontend.css
			'primary_color'           => '#004B61',  // Petrol/Dunkelblau - Hauptfarbe
			'secondary_color'         => '#F0F4F5',  // Helles Grau-Blau - Hintergründe
			'accent_color'            => '#F79D00',  // Orange - Hervorhebungen
			'border_color'            => '#e5e7eb',  // Hellgrau - Rahmen

			// Import.
			'import_auto_brand'       => true,
			'import_batch_size'       => 200,
			'import_max_file_size'    => 10,

			// Frontend.
			'auto_inject_specs'       => false,
			'spec_table_style'        => 'default',
			'show_icons'              => false,
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Input settings.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		// CRITICAL: Load existing settings and merge with new input.
		// This prevents data loss when saving only one tab.
		$existing = get_option( self::OPTION_NAME, $this->get_default_settings() );
		$sanitized = $existing; // Start with existing data.

		// Only sanitize fields that are actually present in $input.
		// This allows partial updates from different tabs.

		// General.
		if ( isset( $input['company_name'] ) ) {
			$sanitized['company_name'] = sanitize_text_field( $input['company_name'] );
		}
		
		if ( isset( $input['company_url'] ) ) {
			$sanitized['company_url'] = esc_url_raw( $input['company_url'] );
		}
		
		if ( isset( $input['default_brand'] ) ) {
			$sanitized['default_brand'] = sanitize_text_field( $input['default_brand'] );
		}
		
		if ( isset( $input['company_logo'] ) ) {
			$sanitized['company_logo'] = esc_url_raw( $input['company_logo'] );
		}

		// Schema.
		if ( isset( $input['schema_org_name'] ) ) {
			$sanitized['schema_org_name'] = sanitize_text_field( $input['schema_org_name'] );
		}
		
		if ( isset( $input['schema_org_url'] ) ) {
			$sanitized['schema_org_url'] = esc_url_raw( $input['schema_org_url'] );
		}
		
		if ( isset( $input['schema_org_description'] ) ) {
			$sanitized['schema_org_description'] = sanitize_textarea_field( $input['schema_org_description'] );
		}
		
		if ( isset( $input['schema_contact_type'] ) ) {
			$sanitized['schema_contact_type'] = sanitize_text_field( $input['schema_contact_type'] );
		}
		
		if ( isset( $input['schema_contact_telephone'] ) ) {
			$sanitized['schema_contact_telephone'] = sanitize_text_field( $input['schema_contact_telephone'] );
		}
		
		if ( isset( $input['schema_contact_email'] ) ) {
			$sanitized['schema_contact_email'] = sanitize_email( $input['schema_contact_email'] );
		}

		// Design.
		if ( isset( $input['primary_color'] ) ) {
			$sanitized['primary_color'] = sanitize_hex_color( $input['primary_color'] );
		}
		
		if ( isset( $input['secondary_color'] ) ) {
			$sanitized['secondary_color'] = sanitize_hex_color( $input['secondary_color'] );
		}
		
		if ( isset( $input['accent_color'] ) ) {
			$sanitized['accent_color'] = sanitize_hex_color( $input['accent_color'] );
		}
		
		if ( isset( $input['border_color'] ) ) {
			$sanitized['border_color'] = sanitize_hex_color( $input['border_color'] );
		}

		// Import.
		if ( isset( $input['import_auto_brand'] ) ) {
			$sanitized['import_auto_brand'] = (bool) $input['import_auto_brand'];
		} elseif ( isset( $_POST['_wp_http_referer'] ) && strpos( sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), 'tab=import' ) !== false ) {
			// Checkbox not set = false (only when saving import tab).
			$sanitized['import_auto_brand'] = false;
		}
		
		if ( isset( $input['import_batch_size'] ) ) {
			$sanitized['import_batch_size'] = absint( $input['import_batch_size'] );
		}
		
		if ( isset( $input['import_max_file_size'] ) ) {
			$sanitized['import_max_file_size'] = absint( $input['import_max_file_size'] );
		}

		// Frontend.
		if ( isset( $input['auto_inject_specs'] ) ) {
			$sanitized['auto_inject_specs'] = (bool) $input['auto_inject_specs'];
		} elseif ( isset( $_POST['_wp_http_referer'] ) && strpos( sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), 'tab=frontend' ) !== false ) {
			// Checkbox not set = false (only when saving frontend tab).
			$sanitized['auto_inject_specs'] = false;
		}
		
		if ( isset( $input['spec_table_style'] ) ) {
			$sanitized['spec_table_style'] = sanitize_text_field( $input['spec_table_style'] );
		}
		
		if ( isset( $input['show_icons'] ) ) {
			$sanitized['show_icons'] = (bool) $input['show_icons'];
		} elseif ( isset( $_POST['_wp_http_referer'] ) && strpos( sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), 'tab=frontend' ) !== false ) {
			// Checkbox not set = false (only when saving frontend tab).
			$sanitized['show_icons'] = false;
		}

		return $sanitized;
	}

	/**
	 * Render settings page
	 *
	 * @return void
	 */
	public function render_settings_page() {
		// Get active tab.
		$this->active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

		?>
		<div class="wrap ayonto-page">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="ayonto-page-subtitle"><?php esc_html_e( 'Konfigurieren Sie alle Einstellungen für Ihren Ayonto Sites Builder.', 'ayonto-sites' ); ?></p>

			<h2 class="nav-tab-wrapper ayonto-settings-tabs">
				<a href="?page=ayonto-settings&tab=general" class="nav-tab <?php echo 'general' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Allgemein', 'ayonto-sites' ); ?>
				</a>
				<a href="?page=ayonto-settings&tab=schema" class="nav-tab <?php echo 'schema' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Schema.org', 'ayonto-sites' ); ?>
				</a>
				<a href="?page=ayonto-settings&tab=design" class="nav-tab <?php echo 'design' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Design', 'ayonto-sites' ); ?>
				</a>
				<a href="?page=ayonto-settings&tab=import" class="nav-tab <?php echo 'import' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Import', 'ayonto-sites' ); ?>
				</a>
				<a href="?page=ayonto-settings&tab=frontend" class="nav-tab <?php echo 'frontend' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Frontend', 'ayonto-sites' ); ?>
				</a>
			</h2>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'ayonto_sites_settings_group' );

				switch ( $this->active_tab ) {
					case 'schema':
						do_settings_sections( 'ayonto-settings-schema' );
						break;
					case 'design':
						do_settings_sections( 'ayonto-settings-design' );
						break;
					case 'import':
						do_settings_sections( 'ayonto-settings-import' );
						break;
					case 'frontend':
						do_settings_sections( 'ayonto-settings-frontend' );
						break;
					default:
						do_settings_sections( 'ayonto-settings-general' );
						break;
				}

				submit_button( __( 'Einstellungen speichern', 'ayonto-sites' ), 'primary ayonto-button' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render section descriptions
	 */
	public function render_general_section_description() {
		echo '<p>' . esc_html__( 'Grundlegende Einstellungen für Ihr Unternehmen und die Marke.', 'ayonto-sites' ) . '</p>';
	}

	public function render_schema_section_description() {
		echo '<p>' . esc_html__( 'Konfigurieren Sie die Schema.org Organisation für strukturierte Daten (SEO).', 'ayonto-sites' ) . '</p>';
	}

	public function render_design_section_description() {
		echo '<p>' . esc_html__( 'Passen Sie Farben und Design-Elemente für das Frontend an.', 'ayonto-sites' ) . '</p>';
	}

	public function render_import_section_description() {
		echo '<p>' . esc_html__( 'Einstellungen für den Datenimport.', 'ayonto-sites' ) . '</p>';
	}

	public function render_frontend_section_description() {
		echo '<p>' . esc_html__( 'Optionen für die Anzeige im Frontend.', 'ayonto-sites' ) . '</p>';
	}

	/**
	 * Render text field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_text_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

		?>
		<input 
			type="text" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
		/>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render URL field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_url_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

		?>
		<input 
			type="url" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
		/>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render email field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_email_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

		?>
		<input 
			type="email" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			class="regular-text"
		/>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render textarea field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_textarea_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';
		$rows        = isset( $args['rows'] ) ? $args['rows'] : 4;

		?>
		<textarea 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			rows="<?php echo esc_attr( $rows ); ?>"
			class="large-text"
		><?php echo esc_textarea( $value ); ?></textarea>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render upload field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_upload_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : '';

		?>
		<input 
			type="url" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			class="regular-text"
		/>
		<button type="button" class="button ayonto-upload-button">
			<?php esc_html_e( 'Logo auswählen', 'ayonto-sites' ); ?>
		</button>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render number field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_number_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : $args['default'];
		$min         = isset( $args['min'] ) ? $args['min'] : 0;
		$max         = isset( $args['max'] ) ? $args['max'] : 999999;

		?>
		<input 
			type="number" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			min="<?php echo esc_attr( $min ); ?>"
			max="<?php echo esc_attr( $max ); ?>"
			class="small-text"
		/>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render select field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_select_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : $args['default'];
		$options     = isset( $args['options'] ) ? $args['options'] : array();

		?>
		<select 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>"
		>
			<?php foreach ( $options as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render checkbox field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : $args['default'];

		?>
		<label for="<?php echo esc_attr( $args['label_for'] ); ?>">
			<input 
				type="checkbox" 
				id="<?php echo esc_attr( $args['label_for'] ); ?>" 
				name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
				value="1"
				<?php checked( $value, true ); ?>
			/>
			<?php
			if ( isset( $args['description'] ) ) {
				echo esc_html( $args['description'] );
			}
			?>
		</label>
		<?php
	}

	/**
	 * Render color field
	 *
	 * @param array $args Field arguments.
	 * @return void
	 */
	public function render_color_field( $args ) {
		$option_name = self::OPTION_NAME;
		$settings    = get_option( $option_name, $this->get_default_settings() );
		$value       = isset( $settings[ $args['label_for'] ] ) ? $settings[ $args['label_for'] ] : $args['default'];

		?>
		<input 
			type="text" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>" 
			name="<?php echo esc_attr( $option_name . '[' . $args['label_for'] . ']' ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			class="ayonto-color-picker"
			data-default-color="<?php echo esc_attr( $args['default'] ); ?>"
		/>
		<span class="ayonto-color-preview" style="background-color: <?php echo esc_attr( $value ); ?>;"></span>
		<?php
		if ( isset( $args['description'] ) ) {
			echo '<p class="ayonto-setting-description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Get a specific setting value
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value if setting doesn't exist.
	 * @return mixed Setting value.
	 */
	public static function get( $key, $default = null ) {
		$settings = get_option( self::OPTION_NAME, array() );
		$defaults = ( new self() )->get_default_settings();

		// Merge with defaults.
		$settings = wp_parse_args( $settings, $defaults );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}
