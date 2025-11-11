<?php
/**
 * Admin functionality
 *
 * @package    Voltrana_Sites
 * @subpackage Admin
 * @since      0.1.0
 */

namespace Voltrana\Sites\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class
 *
 * Handles admin UI, metaboxes, and data saving.
 *
 * @since 0.1.0
 */
class Admin {
	
	/**
	 * Singleton instance
	 *
	 * @var Admin
	 */
	private static $instance = null;
	
	/**
	 * Get singleton instance
	 *
	 * @return Admin
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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_vt_battery', array( $this, 'save_meta_data' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_uploader' ) );
	}
	
	/**
	 * Enqueue WordPress Media Uploader
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_media_uploader( $hook ) {
		// Nur auf vt_battery Edit-Seiten laden.
		if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
			global $post;
			if ( $post && 'vt_battery' === $post->post_type ) {
				wp_enqueue_media();
			}
		}
	}
	
	/**
	 * Add meta boxes
	 *
	 * Build 016: Repeatable Batteries
	 * Build 034: Parent-Page-Auswahl
	 * Build 048: Additional Content (WYSIWYG)
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		// Parent-Page-Auswahl Meta Box (prominent, oben).
		add_meta_box(
			'vt_parent_page',
			__( 'Übergeordnete Seite', 'voltrana-sites' ),
			array( $this, 'render_parent_page_metabox' ),
			'vt_battery',
			'side',
			'high'
		);
		
		// Build 048: Additional Content Meta Box (WYSIWYG).
		add_meta_box(
			'vt_additional_content',
			__( 'Zusätzlicher Inhalt', 'voltrana-sites' ),
			array( $this, 'render_additional_content_metabox' ),
			'vt_battery',
			'normal',
			'high'
		);
		
		// Build 016: Repeatable Batterien Meta Box.
		add_meta_box(
			'vt_batteries_repeater',
			__( 'Batterien für diese Lösung', 'voltrana-sites' ),
			array( $this, 'render_batteries_repeater_metabox' ),
			'vt_battery',
			'normal',
			'high'
		);
	}
	
	/**
	 * Render Parent Page Meta Box (Build 034)
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public function render_parent_page_metabox( $post ) {
		wp_nonce_field( 'vt_parent_page_nonce', 'vt_parent_page_nonce' );
		
		// Get all published pages.
		$args = array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);
		
		$pages = get_posts( $args );
		
		// Get current parent page ID from meta.
		$current_parent_page = get_post_meta( $post->ID, 'vt_parent_page_id', true );
		
		?>
		<p>
			<label for="vt_parent_page_id" style="display: block; margin-bottom: 8px; font-weight: 600;">
				<?php _e( 'Übergeordnete Seite auswählen:', 'voltrana-sites' ); ?>
			</label>
			<select name="vt_parent_page_id" id="vt_parent_page_id" style="width: 100%;">
				<option value="0"><?php _e( '— Keine —', 'voltrana-sites' ); ?></option>
				<?php foreach ( $pages as $page ) : ?>
					<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $current_parent_page, $page->ID ); ?>>
						<?php echo esc_html( $page->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="description" style="font-size: 12px; color: #646970;">
			<?php _e( 'Wählen Sie eine WordPress-Seite als übergeordnete Seite für diese Lösung. Dies beeinflusst die URL-Struktur.', 'voltrana-sites' ); ?>
		</p>
		<?php
	}
	
	/**
	 * Render Additional Content Meta Box (Build 048 - REVISED)
	 * Simple textarea with HTML support (NO wp_editor due to DOM issues)
	 *
	 * WordPress Ticket #19173: wp_editor() breaks when metaboxes are moved in DOM.
	 * Solution: Use textarea with Quicktags for stable, movable metabox.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public function render_additional_content_metabox( $post ) {
		wp_nonce_field( 'vt_additional_content_nonce', 'vt_additional_content_nonce' );
		
		// Get current additional content - ensure it's always a string.
		$additional_content = get_post_meta( $post->ID, 'additional_content', true );
		
		if ( ! is_string( $additional_content ) || empty( $additional_content ) ) {
			$additional_content = '';
		}
		
		?>
		<div class="vt-additional-content-wrapper">
			<p class="description" style="margin-bottom: 10px;">
				<?php _e( 'Dieser Inhalt kann im Frontend über Elementor (Dynamic Tag) oder per Shortcode [vt_additional_content] ausgegeben werden. HTML ist erlaubt (H2-H6, P, Strong, Listen, Links, Tabellen etc.). Tabellen nutzen automatisch das gleiche Styling wie die Batterie-Vergleichstabellen.', 'voltrana-sites' ); ?>
			</p>
			
			<div class="vt-editor-toolbar" style="margin-bottom: 5px;">
				<button type="button" class="button vt-insert-tag" data-tag="h2" title="<?php esc_attr_e( 'Überschrift 2', 'voltrana-sites' ); ?>">H2</button>
				<button type="button" class="button vt-insert-tag" data-tag="h3" title="<?php esc_attr_e( 'Überschrift 3', 'voltrana-sites' ); ?>">H3</button>
				<button type="button" class="button vt-insert-tag" data-tag="p" title="<?php esc_attr_e( 'Absatz', 'voltrana-sites' ); ?>">P</button>
				<button type="button" class="button vt-insert-tag" data-tag="strong" title="<?php esc_attr_e( 'Fett', 'voltrana-sites' ); ?>"><strong>B</strong></button>
				<button type="button" class="button vt-insert-tag" data-tag="em" title="<?php esc_attr_e( 'Kursiv', 'voltrana-sites' ); ?>"><em>I</em></button>
				<button type="button" class="button vt-insert-list" data-type="ul" title="<?php esc_attr_e( 'Aufzählungsliste', 'voltrana-sites' ); ?>">• Liste</button>
				<button type="button" class="button vt-insert-list" data-type="ol" title="<?php esc_attr_e( 'Nummerierte Liste', 'voltrana-sites' ); ?>">1. Liste</button>
				<button type="button" class="button vt-insert-table" title="<?php esc_attr_e( 'Tabelle einfügen', 'voltrana-sites' ); ?>">📊 Tabelle</button>
				<button type="button" class="button vt-insert-link" title="<?php esc_attr_e( 'Link einfügen', 'voltrana-sites' ); ?>">🔗 Link</button>
			</div>
			
			<textarea 
				name="vt_additional_content" 
				id="vt_additional_content" 
				rows="12" 
				style="width: 100%; font-family: monospace; font-size: 13px;"
				placeholder="<?php esc_attr_e( 'Hier HTML-Content eingeben...', 'voltrana-sites' ); ?>"
			><?php echo esc_textarea( $additional_content ); ?></textarea>
			
			<p class="description" style="margin-top: 8px; font-size: 11px; color: #646970;">
				<strong><?php _e( 'Erlaubte Tags:', 'voltrana-sites' ); ?></strong> 
				&lt;h2&gt; &lt;h3&gt; &lt;h4&gt; &lt;h5&gt; &lt;h6&gt; &lt;p&gt; &lt;strong&gt; &lt;b&gt; &lt;em&gt; &lt;i&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;a href=""&gt; &lt;br&gt; &lt;span&gt; &lt;div&gt; &lt;table&gt; &lt;tr&gt; &lt;th&gt; &lt;td&gt;<br>
				<strong><?php _e( 'Tabellen-Styling:', 'voltrana-sites' ); ?></strong> 
				Nutze <code>class="vt-battery-table"</code> für professionelles Styling (dunkler Header, Hover-Effekt, responsive)
			</p>
		</div>
		
		<style>
		.vt-additional-content-wrapper {
			padding: 0;
		}
		.vt-editor-toolbar {
			display: flex;
			gap: 4px;
			flex-wrap: wrap;
		}
		.vt-editor-toolbar .button {
			padding: 2px 8px;
			font-size: 12px;
			height: auto;
			line-height: 1.4;
		}
		#vt_additional_content {
			border: 1px solid #dcdcde;
			border-radius: 3px;
			padding: 8px;
			line-height: 1.6;
		}
		#vt_additional_content:focus {
			border-color: #2271b1;
			box-shadow: 0 0 0 1px #2271b1;
			outline: 2px solid transparent;
		}
		</style>
		
		<script>
		jQuery(document).ready(function($) {
			var textarea = $('#vt_additional_content');
			
			// Insert tag
			$('.vt-insert-tag').on('click', function(e) {
				e.preventDefault();
				var tag = $(this).data('tag');
				var selected = getSelectedText(textarea[0]);
				var text = selected || 'Text hier';
				insertAtCursor(textarea[0], '<' + tag + '>' + text + '</' + tag + '>');
			});
			
			// Insert list
			$('.vt-insert-list').on('click', function(e) {
				e.preventDefault();
				var type = $(this).data('type');
				var template = '<' + type + '>\n  <li>Punkt 1</li>\n  <li>Punkt 2</li>\n  <li>Punkt 3</li>\n</' + type + '>';
				insertAtCursor(textarea[0], template);
			});
			
			// Insert table
			$('.vt-insert-table').on('click', function(e) {
				e.preventDefault();
				var template = '<div class="vt-battery-table-wrapper">\n' +
					'<table class="vt-battery-table">\n' +
					'  <thead>\n' +
					'    <tr>\n' +
					'      <th>Spalte 1</th>\n' +
					'      <th>Spalte 2</th>\n' +
					'      <th>Spalte 3</th>\n' +
					'    </tr>\n' +
					'  </thead>\n' +
					'  <tbody>\n' +
					'    <tr>\n' +
					'      <td>Zeile 1, Spalte 1</td>\n' +
					'      <td>Zeile 1, Spalte 2</td>\n' +
					'      <td>Zeile 1, Spalte 3</td>\n' +
					'    </tr>\n' +
					'    <tr>\n' +
					'      <td>Zeile 2, Spalte 1</td>\n' +
					'      <td>Zeile 2, Spalte 2</td>\n' +
					'      <td>Zeile 2, Spalte 3</td>\n' +
					'    </tr>\n' +
					'  </tbody>\n' +
					'</table>\n' +
					'</div>';
				insertAtCursor(textarea[0], template);
			});
			
			// Insert link
			$('.vt-insert-link').on('click', function(e) {
				e.preventDefault();
				var url = prompt('<?php esc_attr_e( 'URL eingeben:', 'voltrana-sites' ); ?>', 'https://');
				if (url) {
					var selected = getSelectedText(textarea[0]);
					var text = selected || 'Link-Text';
					insertAtCursor(textarea[0], '<a href="' + url + '">' + text + '</a>');
				}
			});
			
			function getSelectedText(textarea) {
				var start = textarea.selectionStart;
				var end = textarea.selectionEnd;
				return textarea.value.substring(start, end);
			}
			
			function insertAtCursor(textarea, text) {
				var start = textarea.selectionStart;
				var end = textarea.selectionEnd;
				var value = textarea.value;
				
				textarea.value = value.substring(0, start) + text + value.substring(end);
				
				// Set cursor position after inserted text
				var newPos = start + text.length;
				textarea.selectionStart = newPos;
				textarea.selectionEnd = newPos;
				textarea.focus();
			}
		});
		</script>
		<?php
	}
	
	/**
	 * Render Repeatable Batteries Meta Box (Build 016)
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public function render_batteries_repeater_metabox( $post ) {
		wp_nonce_field( 'vt_batteries_nonce', 'vt_batteries_nonce' );
		
		$batteries = get_post_meta( $post->ID, 'vt_batteries', true );
		if ( ! is_array( $batteries ) ) {
			$batteries = array();
		}
		
		?>
		<div id="vt-batteries-repeater">
			<div id="vt-batteries-list">
				<?php
				if ( empty( $batteries ) ) {
					// Show one empty row by default.
					$this->render_battery_row( 0, array() );
				} else {
					foreach ( $batteries as $index => $battery ) {
						$this->render_battery_row( $index, $battery );
					}
				}
				?>
			</div>
			
			<p>
				<button type="button" class="button vt-add-battery">
					<?php _e( '+ Weitere Batterie hinzufügen', 'voltrana-sites' ); ?>
				</button>
			</p>
		</div>
		
		<style>
		.vt-battery-row {
			background: #f9f9f9;
			border: 1px solid #ddd;
			padding: 10px;
			margin-bottom: 12px;
			position: relative;
		}
		.vt-battery-row-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10px;
			padding-bottom: 6px;
			border-bottom: 1px solid #ddd;
		}
		.vt-battery-row-header h4 {
			margin: 0;
			font-size: 13px;
		}
		.vt-remove-battery {
			color: #b32d2e;
			padding: 3px 8px;
			font-size: 12px;
		}
		.vt-battery-fields {
			display: grid;
			grid-template-columns: repeat(8, 1fr);
			gap: 8px;
			align-items: start;
		}
		.vt-battery-field {
			display: flex;
			flex-direction: column;
		}
		.vt-battery-field label {
			font-weight: 600;
			margin-bottom: 2px;
			font-size: 11px;
			color: #1d2327;
			line-height: 1.3;
		}
		.vt-battery-field input,
		.vt-battery-field select,
		.vt-battery-field textarea {
			width: 100%;
			font-size: 12px;
			padding: 3px 6px;
			line-height: 1.4;
		}
		.vt-battery-field textarea {
			min-height: 40px;
			resize: vertical;
		}
		.vt-battery-field input[type="text"] {
			max-width: 180px;
		}
		.vt-battery-field input[type="url"] {
			max-width: 300px;
		}
		.vt-battery-field input[type="number"] {
			max-width: 100px;
		}
		/* Wide fields - für 8-Spalten-Layout */
		.vt-field-wide {
			grid-column: span 2;
		}
		.vt-field-triple {
			grid-column: span 3;
		}
		.vt-field-full {
			grid-column: span 8;
		}
		/* Dimension group */
		.vt-dimensions-group {
			display: flex;
			gap: 6px;
			align-items: flex-end;
		}
		.vt-dimensions-group input {
			width: 70px;
			padding: 3px 4px;
		}
		.vt-dimensions-group .dimension-label {
			font-size: 10px;
			color: #646970;
			margin-bottom: 1px;
			line-height: 1.2;
		}
		.vt-dimensions-group .dimension-field {
			flex: 1;
		}
		.vt-dimensions-group .dimension-separator {
			margin: 0 2px;
			padding-bottom: 3px;
			font-size: 13px;
			color: #646970;
		}
		/* Section headers */
		.vt-section-header {
			grid-column: span 8;
			font-size: 12px;
			font-weight: 600;
			color: #004B61;
			margin-top: 6px;
			margin-bottom: 0;
			padding-bottom: 3px;
			border-bottom: 1px solid #dcdcde;
		}
		.vt-section-header:first-child {
			margin-top: 0;
		}
		/* 8-Spalten Layout Headers */
		.vt-section-header-third-narrow {
			grid-column: span 3;
			font-size: 12px;
			font-weight: 600;
			color: #004B61;
			margin-top: 0;
			margin-bottom: 0;
			padding-bottom: 3px;
			border-bottom: 1px solid #dcdcde;
		}
		.vt-section-header-middle {
			grid-column: span 3;
			font-size: 12px;
			font-weight: 600;
			color: #004B61;
			margin-top: 0;
			margin-bottom: 0;
			padding-bottom: 3px;
			border-bottom: 1px solid #dcdcde;
		}
		.vt-section-header-third-small {
			grid-column: span 2;
			font-size: 12px;
			font-weight: 600;
			color: #004B61;
			margin-top: 0;
			margin-bottom: 0;
			padding-bottom: 3px;
			border-bottom: 1px solid #dcdcde;
		}
		/* Media field */
		.vt-media-field {
			display: flex;
			gap: 6px;
			align-items: center;
		}
		.vt-media-field input {
			flex: 1;
		}
		.vt-media-field .button {
			flex-shrink: 0;
			padding: 3px 8px;
			font-size: 12px;
		}
		.vt-image-preview {
			margin-right: 8px;
		}
		.vt-image-preview img {
			display: block;
		}
		/* Tech fields - volle Breite */
		.vt-tech-field input,
		.vt-tech-field select {
			max-width: 100% !important;
			width: 100% !important;
		}
		</style>
		
		<script>
		jQuery(document).ready(function($) {
			let batteryIndex = <?php echo count( $batteries ); ?>;
			
			// Add battery
			$('.vt-add-battery').on('click', function() {
				const template = `<?php echo addslashes( $this->get_battery_row_template( '__INDEX__' ) ); ?>`;
				const html = template.replace(/__INDEX__/g, batteryIndex);
				$('#vt-batteries-list').append(html);
				batteryIndex++;
			});
			
			// Remove battery
			$(document).on('click', '.vt-remove-battery', function() {
				if (confirm('<?php _e( 'Batterie wirklich löschen?', 'voltrana-sites' ); ?>')) {
					$(this).closest('.vt-battery-row').remove();
				}
			});
			
			// Image Media Uploader (Build 055b: WordPress-Standard-konform)
			$(document).on('click', '.vt-upload-image-button', function(e) {
				e.preventDefault();
				
				const button = $(this);
				const rowContainer = button.closest('.vt-battery-field');
				const inputField = rowContainer.find('.vt-product-image-id');
				const previewContainer = rowContainer.find('.vt-image-preview');
				const removeButton = rowContainer.find('.vt-remove-image-button');
				
				// Debug
				console.log('Upload button clicked');
				console.log('Input field found:', inputField.length);
				console.log('Input field selector:', inputField.attr('name'));
				
				// WordPress Media Frame erstellen
				const frame = wp.media({
					title: '<?php _e( 'Produktbild wählen', 'voltrana-sites' ); ?>',
					button: {
						text: '<?php _e( 'Bild verwenden', 'voltrana-sites' ); ?>'
					},
					library: {
						type: 'image'
					},
					multiple: false
				});
				
				// Wenn ein Bild ausgewählt wurde
				frame.on('select', function() {
					const attachment = frame.state().get('selection').first().toJSON();
					
					console.log('Image selected:', attachment);
					console.log('Image ID:', attachment.id);
					
					// Nur Bilder erlauben
					if (attachment.type !== 'image') {
						alert('<?php _e( 'Bitte wählen Sie nur Bilddateien!', 'voltrana-sites' ); ?>');
						return;
					}
					
					// ID speichern
					inputField.val(attachment.id);
					console.log('Input field value set to:', inputField.val());
					
					// Preview anzeigen
					const thumbUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
					previewContainer.html('<img src="' + thumbUrl + '" style="max-width:80px;height:auto;border:1px solid #ddd;border-radius:3px;">').show();
					removeButton.show();
					
					console.log('Preview updated');
				});
				
				frame.open();
			});
			
			// Image entfernen
			$(document).on('click', '.vt-remove-image-button', function(e) {
				e.preventDefault();
				
				const button = $(this);
				const rowContainer = button.closest('.vt-battery-field');
				const inputField = rowContainer.find('.vt-product-image-id');
				const previewContainer = rowContainer.find('.vt-image-preview');
				
				console.log('Remove button clicked');
				console.log('Removing image ID:', inputField.val());
				
				inputField.val('');
				previewContainer.html('').hide();
				button.hide();
				
				console.log('Image removed');
			});
		});
		</script>
		<?php
	}
	
	/**
	 * Render single battery row
	 *
	 * @param int   $index   Row index.
	 * @param array $battery Battery data.
	 * @return void
	 */
	private function render_battery_row( $index, $battery ) {
		// Handle both numeric index and string placeholder.
		$display_number = is_numeric( $index ) ? ( $index + 1 ) : 'X';
		
		// Get current values.
		$model            = isset( $battery['model'] ) ? $battery['model'] : '';
		$ean              = isset( $battery['ean'] ) ? $battery['ean'] : '';
		$series           = isset( $battery['series'] ) ? $battery['series'] : '';
		$technology       = isset( $battery['technology'] ) ? $battery['technology'] : '';
		$capacity_ah      = isset( $battery['capacity_ah'] ) ? $battery['capacity_ah'] : '';
		$voltage_v        = isset( $battery['voltage_v'] ) ? $battery['voltage_v'] : '';
		$cca_a            = isset( $battery['cca_a'] ) ? $battery['cca_a'] : '';
		$dimensions_l     = isset( $battery['dimensions_l'] ) ? $battery['dimensions_l'] : '';
		$dimensions_w     = isset( $battery['dimensions_w'] ) ? $battery['dimensions_w'] : '';
		$dimensions_h     = isset( $battery['dimensions_h'] ) ? $battery['dimensions_h'] : '';
		$weight_kg        = isset( $battery['weight_kg'] ) ? $battery['weight_kg'] : '';
		$terminals        = isset( $battery['terminals'] ) ? $battery['terminals'] : '';
		$circuit_type     = isset( $battery['circuit_type'] ) ? $battery['circuit_type'] : '';
		$warranty_months  = isset( $battery['warranty_months'] ) ? $battery['warranty_months'] : '';
		$product_image_id = isset( $battery['product_image_id'] ) ? $battery['product_image_id'] : ''; // Build 055.
		
		// Properties array to string.
		$properties = '';
		if ( isset( $battery['properties'] ) ) {
			if ( is_array( $battery['properties'] ) ) {
				$properties = implode( ', ', $battery['properties'] );
			} else {
				$properties = $battery['properties'];
			}
		}
		
		?>
		<div class="vt-battery-row">
			<div class="vt-battery-row-header">
				<h4><?php printf( __( 'Batterie %s', 'voltrana-sites' ), $display_number ); ?></h4>
				<button type="button" class="button vt-remove-battery">
					<?php _e( '✕ Löschen', 'voltrana-sites' ); ?>
				</button>
			</div>
			<div class="vt-battery-fields">
				
				<!-- 3 Headers nebeneinander: Grunddaten (3 Spalten) | Maße & Gewicht (3 Spalten) | Sonstiges (2 Spalten) -->
				<div class="vt-section-header-third-narrow"><?php _e( 'Grunddaten', 'voltrana-sites' ); ?></div>
				<div class="vt-section-header-middle"><?php _e( 'Maße & Gewicht', 'voltrana-sites' ); ?></div>
				<div class="vt-section-header-third-small"><?php _e( 'Sonstiges', 'voltrana-sites' ); ?></div>
				
				<!-- ZEILE 1: ALLE FELDER HORIZONTAL (8 Spalten) -->
				
				<!-- Spalte 1: Modell -->
				<div class="vt-battery-field">
					<label><?php _e( 'Modell', 'voltrana-sites' ); ?> <span style="color:#d63638;">*</span></label>
					<input type="text" name="vt_batteries[<?php echo $index; ?>][model]" value="<?php echo esc_attr( $model ); ?>" required>
				</div>
				
				<!-- Spalte 2: EAN -->
				<div class="vt-battery-field">
					<label><?php _e( 'EAN', 'voltrana-sites' ); ?></label>
					<input type="text" name="vt_batteries[<?php echo $index; ?>][ean]" value="<?php echo esc_attr( $ean ); ?>">
				</div>
				
				<!-- Spalte 3: Serie -->
				<div class="vt-battery-field">
					<label><?php _e( 'Serie', 'voltrana-sites' ); ?></label>
					<input type="text" name="vt_batteries[<?php echo $index; ?>][series]" value="<?php echo esc_attr( $series ); ?>">
				</div>
				
				<!-- Spalte 4-5: L×B×H inline (span 2) - OHNE Labels über Inputs, OHNE × Zeichen -->
				<div class="vt-battery-field vt-field-wide">
					<label><?php _e( 'L × B × H (mm)', 'voltrana-sites' ); ?></label>
					<div class="vt-dimensions-group">
						<div class="dimension-field">
							<input type="number" name="vt_batteries[<?php echo $index; ?>][dimensions_l]" value="<?php echo esc_attr( $dimensions_l ); ?>" step="0.01" placeholder="L">
						</div>
						<div class="dimension-field">
							<input type="number" name="vt_batteries[<?php echo $index; ?>][dimensions_w]" value="<?php echo esc_attr( $dimensions_w ); ?>" step="0.01" placeholder="B">
						</div>
						<div class="dimension-field">
							<input type="number" name="vt_batteries[<?php echo $index; ?>][dimensions_h]" value="<?php echo esc_attr( $dimensions_h ); ?>" step="0.01" placeholder="H">
						</div>
					</div>
				</div>
				
				<!-- Spalte 6: Gewicht -->
				<div class="vt-battery-field">
					<label><?php _e( 'Gewicht (kg)', 'voltrana-sites' ); ?></label>
					<input type="number" name="vt_batteries[<?php echo $index; ?>][weight_kg]" value="<?php echo esc_attr( $weight_kg ); ?>" step="0.01">
				</div>
				
				<!-- Spalte 7-8: Produktbild (span 2) - Build 055: product_image statt datasheet_url -->
				<div class="vt-battery-field vt-field-wide">
					<label><?php _e( 'Produktbild', 'voltrana-sites' ); ?></label>
					<div class="vt-media-field">
						<input type="hidden" class="vt-product-image-id" name="vt_batteries[<?php echo $index; ?>][product_image_id]" value="<?php echo esc_attr( $product_image_id ); ?>">
						<div class="vt-image-preview" style="<?php echo ! empty( $product_image_id ) ? '' : 'display:none;'; ?>">
							<?php
							if ( ! empty( $product_image_id ) ) {
								$thumb_url = wp_get_attachment_image_url( $product_image_id, 'thumbnail' );
								if ( $thumb_url ) {
									echo '<img src="' . esc_url( $thumb_url ) . '" style="max-width:80px;height:auto;border:1px solid #ddd;border-radius:3px;">';
								}
							}
							?>
						</div>
						<button type="button" class="button vt-upload-image-button" data-index="<?php echo $index; ?>">
							<?php _e( 'Bild wählen', 'voltrana-sites' ); ?>
						</button>
						<button type="button" class="button vt-remove-image-button" data-index="<?php echo $index; ?>" style="<?php echo empty( $battery['product_image_id'] ) ? 'display:none;' : ''; ?>">
							<?php _e( '✕', 'voltrana-sites' ); ?>
						</button>
					</div>
				</div>
				
				<!-- Hidden: Marke immer "<?php echo esc_attr( Settings_Helper::get_default_brand() ); ?>" -->
				<input type="hidden" name="vt_batteries[<?php echo $index; ?>][brand]" value="<?php echo esc_attr( Settings_Helper::get_default_brand() ); ?>">
				
				<!-- Technische Spezifikationen - Header über volle Breite -->
				<div class="vt-section-header"><?php _e( 'Technische Spezifikationen', 'voltrana-sites' ); ?></div>
				
				<!-- ZEILE 2: TECH SPECS - ALLE HORIZONTAL (8 Spalten) -->
				
				<div class="vt-battery-field">
					<label><?php _e( 'Technologie', 'voltrana-sites' ); ?></label>
					<select name="vt_batteries[<?php echo $index; ?>][technology]">
						<option value="">— <?php _e( 'Wählen', 'voltrana-sites' ); ?> —</option>
						<option value="AGM" <?php selected( $technology, 'AGM' ); ?>>AGM</option>
						<option value="GEL" <?php selected( $technology, 'GEL' ); ?>>GEL</option>
						<option value="EFB" <?php selected( $technology, 'EFB' ); ?>>EFB</option>
						<option value="LiFePO4" <?php selected( $technology, 'LiFePO4' ); ?>>LiFePO4</option>
						<option value="Blei-Säure" <?php selected( $technology, 'Blei-Säure' ); ?>>Blei-Säure</option>
						<option value="Säure" <?php selected( $technology, 'Säure' ); ?>>Säure</option>
					</select>
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'Kapazität (Ah)', 'voltrana-sites' ); ?></label>
					<input type="number" name="vt_batteries[<?php echo $index; ?>][capacity_ah]" value="<?php echo esc_attr( $capacity_ah ); ?>" step="0.01">
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'Spannung (V)', 'voltrana-sites' ); ?></label>
					<select name="vt_batteries[<?php echo $index; ?>][voltage_v]">
						<option value="">— <?php _e( 'Wählen', 'voltrana-sites' ); ?> —</option>
						<option value="6" <?php selected( $voltage_v, '6' ); ?>>6V</option>
						<option value="8" <?php selected( $voltage_v, '8' ); ?>>8V</option>
						<option value="12" <?php selected( $voltage_v, '12' ); ?>>12V</option>
						<option value="24" <?php selected( $voltage_v, '24' ); ?>>24V</option>
						<option value="48" <?php selected( $voltage_v, '48' ); ?>>48V</option>
					</select>
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'CCA (A)', 'voltrana-sites' ); ?></label>
					<input type="number" name="vt_batteries[<?php echo $index; ?>][cca_a]" value="<?php echo esc_attr( $cca_a ); ?>" step="1">
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'Schaltung', 'voltrana-sites' ); ?></label>
					<select name="vt_batteries[<?php echo $index; ?>][circuit_type]">
						<option value="">— <?php _e( 'Wählen', 'voltrana-sites' ); ?> —</option>
						<option value="0" <?php selected( $circuit_type, '0' ); ?>>0</option>
						<option value="1" <?php selected( $circuit_type, '1' ); ?>>1</option>
						<option value="diagonal" <?php selected( $circuit_type, 'diagonal' ); ?>>Diagonal</option>
						<option value="serie" <?php selected( $circuit_type, 'serie' ); ?>>Serie</option>
						<option value="parallel" <?php selected( $circuit_type, 'parallel' ); ?>>Parallel</option>
					</select>
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'Pole/Klemmen', 'voltrana-sites' ); ?></label>
					<input type="text" name="vt_batteries[<?php echo $index; ?>][terminals]" value="<?php echo esc_attr( $terminals ); ?>">
				</div>
				
				<div class="vt-battery-field">
					<label><?php _e( 'Garantie (Mon.)', 'voltrana-sites' ); ?></label>
					<input type="number" name="vt_batteries[<?php echo $index; ?>][warranty_months]" value="<?php echo esc_attr( $warranty_months ); ?>" step="1">
				</div>
				
				<!-- Spalte 8: Eigenschaften (von oben nach unten verschoben) -->
				<div class="vt-battery-field">
					<label><?php _e( 'Eigenschaften', 'voltrana-sites' ); ?></label>
					<textarea name="vt_batteries[<?php echo $index; ?>][properties]" rows="1" placeholder="Deep Cycle, VRLA"><?php echo esc_textarea( $properties ); ?></textarea>
				</div>
				
				</div>
			</div>
			<?php
	}
	
	/**
	 * Get battery row template for JavaScript
	 *
	 * @param string $index Placeholder index.
	 * @return string HTML template.
	 */
	private function get_battery_row_template( $index ) {
		ob_start();
		$this->render_battery_row( $index, array() );
		return str_replace( array( "\n", "\r" ), '', ob_get_clean() );
	}
	
	/**
	 * Get battery fields definition
	 *
	 * @return array Fields definition.
	 */
	private function get_battery_fields() {
		return array(
			'model'            => array(
				'label' => __( 'Modell', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'ean'              => array(
				'label' => __( 'EAN', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'brand'            => array(
				'label' => __( 'Marke', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'series'           => array(
				'label' => __( 'Serie', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'technology'       => array(
				'label'   => __( 'Technologie', 'voltrana-sites' ),
				'type'    => 'select',
				'options' => array(
					'AGM'      => 'AGM',
					'GEL'      => 'GEL',
					'EFB'      => 'EFB',
					'LiFePO4'  => 'LiFePO4',
					'Blei-Säure' => 'Blei-Säure',
					'Säure'    => 'Säure',
				),
			),
			'capacity_ah'      => array(
				'label' => __( 'Kapazität (Ah)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '0.01',
			),
			'voltage_v'        => array(
				'label'   => __( 'Spannung (V)', 'voltrana-sites' ),
				'type'    => 'select',
				'options' => array(
					'6'  => '6V',
					'8'  => '8V',
					'12' => '12V',
					'24' => '24V',
					'48' => '48V',
				),
			),
			'cca_a'            => array(
				'label' => __( 'Kaltstartstrom (A)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '1',
			),
			'dimensions_l'     => array(
				'label' => __( 'Länge (mm)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '0.01',
			),
			'dimensions_w'     => array(
				'label' => __( 'Breite (mm)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '0.01',
			),
			'dimensions_h'     => array(
				'label' => __( 'Höhe (mm)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '0.01',
			),
			'weight_kg'        => array(
				'label' => __( 'Gewicht (kg)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '0.01',
			),
			'terminals'        => array(
				'label' => __( 'Pole/Klemmen', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'circuit_type'     => array(
				'label'   => __( 'Schaltung', 'voltrana-sites' ),
				'type'    => 'select',
				'options' => array(
					'0'        => '0',
					'1'        => '1',
					'diagonal' => 'Diagonal',
					'serie'    => 'Serie',
					'parallel' => 'Parallel',
				),
			),
			'product_group'    => array(
				'label' => __( 'Produktgruppe', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'application_area' => array(
				'label' => __( 'Anwendungsbereich', 'voltrana-sites' ),
				'type'  => 'text',
			),
			'properties'       => array(
				'label' => __( 'Eigenschaften (komma-getrennt)', 'voltrana-sites' ),
				'type'  => 'textarea',
			),
			'warranty_months'  => array(
				'label' => __( 'Garantie (Monate)', 'voltrana-sites' ),
				'type'  => 'number',
				'step'  => '1',
			),
			'product_image_id' => array( // Build 055: NEW!
				'label' => __( 'Produktbild', 'voltrana-sites' ),
				'type'  => 'number',
			),
			'datasheet_url'    => array(
				'label' => __( 'Datenblatt-URL', 'voltrana-sites' ),
				'type'  => 'url',
			),
		);
	}
	
	/**
	 * Save meta data (Build 016, Build 034, Build 048)
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save_meta_data( $post_id, $post ) {
		// Check nonce for batteries.
		if ( ! isset( $_POST['vt_batteries_nonce'] ) || ! wp_verify_nonce( $_POST['vt_batteries_nonce'], 'vt_batteries_nonce' ) ) {
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
		
		// Build 034: Save parent page ID.
		if ( isset( $_POST['vt_parent_page_nonce'] ) && wp_verify_nonce( $_POST['vt_parent_page_nonce'], 'vt_parent_page_nonce' ) ) {
			if ( isset( $_POST['vt_parent_page_id'] ) ) {
				$parent_page_id = absint( $_POST['vt_parent_page_id'] );
				if ( $parent_page_id > 0 ) {
					update_post_meta( $post_id, 'vt_parent_page_id', $parent_page_id );
				} else {
					delete_post_meta( $post_id, 'vt_parent_page_id' );
				}
			}
		}
		
		// Build 048: Save additional content (WYSIWYG).
		if ( isset( $_POST['vt_additional_content_nonce'] ) && wp_verify_nonce( $_POST['vt_additional_content_nonce'], 'vt_additional_content_nonce' ) ) {
			if ( isset( $_POST['vt_additional_content'] ) ) {
				// Use wp_kses_post to allow safe HTML.
				$additional_content = wp_kses_post( $_POST['vt_additional_content'] );
				
				if ( ! empty( $additional_content ) ) {
					update_post_meta( $post_id, 'additional_content', $additional_content );
				} else {
					delete_post_meta( $post_id, 'additional_content' );
				}
			}
		}
		
		// Save batteries.
		if ( isset( $_POST['vt_batteries'] ) && is_array( $_POST['vt_batteries'] ) ) {
			$batteries = array();
			
			foreach ( $_POST['vt_batteries'] as $battery ) {
				$clean_battery = array();
				
				// Sanitize each field.
				$clean_battery['model']            = sanitize_text_field( $battery['model'] ?? '' );
				$clean_battery['ean']              = sanitize_text_field( $battery['ean'] ?? '' );
				$clean_battery['brand']            = Settings_Helper::get_default_brand(); // From settings.
				$clean_battery['series']           = sanitize_text_field( $battery['series'] ?? '' );
				$clean_battery['technology']       = sanitize_text_field( $battery['technology'] ?? '' );
				$clean_battery['capacity_ah']      = floatval( $battery['capacity_ah'] ?? 0 );
				$clean_battery['voltage_v']        = absint( $battery['voltage_v'] ?? 0 );
				$clean_battery['cca_a']            = floatval( $battery['cca_a'] ?? 0 );
				$clean_battery['dimensions_l']     = floatval( $battery['dimensions_l'] ?? 0 );
				$clean_battery['dimensions_w']     = floatval( $battery['dimensions_w'] ?? 0 );
				$clean_battery['dimensions_h']     = floatval( $battery['dimensions_h'] ?? 0 );
				$clean_battery['weight_kg']        = floatval( $battery['weight_kg'] ?? 0 );
				$clean_battery['terminals']        = sanitize_text_field( $battery['terminals'] ?? '' );
				$clean_battery['circuit_type']     = sanitize_text_field( $battery['circuit_type'] ?? '' );
				$clean_battery['warranty_months']  = absint( $battery['warranty_months'] ?? 0 );
				$clean_battery['product_image_id'] = absint( $battery['product_image_id'] ?? 0 ); // Build 055a: NEW!
				$clean_battery['datasheet_url']    = esc_url_raw( $battery['datasheet_url'] ?? '' );
				
				// Properties (comma-separated to array).
				if ( ! empty( $battery['properties'] ) ) {
					$props = array_map( 'trim', explode( ',', $battery['properties'] ) );
					$props = array_filter( $props );
					$clean_battery['properties'] = array_values( array_unique( $props ) );
				} else {
					$clean_battery['properties'] = array();
				}
				
				// Only add if has at least a model.
				if ( ! empty( $clean_battery['model'] ) ) {
					$batteries[] = $clean_battery;
				}
			}
			
			update_post_meta( $post_id, 'vt_batteries', $batteries );
		} else {
			delete_post_meta( $post_id, 'vt_batteries' );
		}
	}
}

