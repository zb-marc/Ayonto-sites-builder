/**
 * Ayonto Settings Enhancements
 *
 * @package    Ayonto_Sites
 * @since      0.1.45
 */

(function($) {
	'use strict';

	/**
	 * Logo Preview Handler
	 */
	function initLogoPreview() {
		const logoInput = $('#company_logo');
		if (!logoInput.length) return;

		// Create preview container if doesn't exist
		if (!$('.ayonto-logo-preview-wrapper').length) {
			logoInput.after(
				'<div class="ayonto-logo-preview-wrapper">' +
					'<div class="ayonto-logo-preview">' +
						'<div class="ayonto-logo-preview-empty">' +
							'<span class="dashicons dashicons-format-image"></span>' +
							'<p>Kein Logo ausgewählt</p>' +
						'</div>' +
					'</div>' +
				'</div>'
			);
		}

		// Update preview when logo URL changes
		function updateLogoPreview() {
			const logoUrl = logoInput.val();
			const preview = $('.ayonto-logo-preview');

			if (logoUrl) {
				preview.html('<img src="' + logoUrl + '" alt="Logo Preview" />');
			} else {
				preview.html(
					'<div class="ayonto-logo-preview-empty">' +
						'<span class="dashicons dashicons-format-image"></span>' +
						'<p>Kein Logo ausgewählt</p>' +
					'</div>'
				);
			}
		}

		// Initial preview
		updateLogoPreview();

		// Update on input change
		logoInput.on('change input', updateLogoPreview);

		// Update when media uploader is used
		$(document).on('click', '.ayonto-upload-button', function() {
			setTimeout(updateLogoPreview, 500);
		});
	}

	/**
	 * Color Picker Enhancement
	 */
	function initColorPickers() {
		$('input[type="text"].color-picker').each(function() {
			if ($(this).val()) {
				$(this).css('border-left', '4px solid ' + $(this).val());
			}

			$(this).on('input change', function() {
				const color = $(this).val();
				if (color.match(/^#[0-9A-F]{6}$/i)) {
					$(this).css('border-left', '4px solid ' + color);
				}
			});
		});
	}

	/**
	 * Form Field Icons (Auto-add based on field type)
	 */
	function addFieldIcons() {
		$('.ayonto-setting-label label').each(function() {
			const $label = $(this);
			const labelText = $label.text().trim();
			let icon = 'admin-generic';

			// Determine icon based on label text
			if (labelText.match(/Firmen|Unternehmen|Organisation/i)) {
				icon = 'building';
			} else if (labelText.match(/URL|Link/i)) {
				icon = 'admin-links';
			} else if (labelText.match(/Logo|Bild/i)) {
				icon = 'format-image';
			} else if (labelText.match(/Marke|Brand/i)) {
				icon = 'tag';
			} else if (labelText.match(/Farbe|Color/i)) {
				icon = 'art';
			} else if (labelText.match(/E-?Mail/i)) {
				icon = 'email';
			} else if (labelText.match(/Telefon|Phone/i)) {
				icon = 'phone';
			} else if (labelText.match(/Beschreibung|Description/i)) {
				icon = 'text-page';
			} else if (labelText.match(/Import/i)) {
				icon = 'upload';
			} else if (labelText.match(/Batch|Größe|Size/i)) {
				icon = 'performance';
			}

			// Add icon if not already present
			if (!$label.parent().find('.ayonto-setting-label-icon').length) {
				$label.parent().prepend(
					'<span class="ayonto-setting-label-icon">' +
						'<span class="dashicons dashicons-' + icon + '"></span>' +
					'</span>'
				);
			}
		});
	}

	/**
	 * Smooth Scroll to Active Tab
	 */
	function initSmoothScroll() {
		$('.ayonto-settings-tabs a').on('click', function() {
			// Scroll to top smoothly
			$('html, body').animate({
				scrollTop: $('.wrap.ayonto-page').offset().top - 50
			}, 300);
		});
	}

	/**
	 * Initialize all enhancements
	 */
	$(document).ready(function() {
		initLogoPreview();
		initColorPickers();
		addFieldIcons();
		initSmoothScroll();

		console.log('Ayonto Settings Enhancements initialized');
	});

})(jQuery);
