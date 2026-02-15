/**
 * GLightbox Initialization for Ayonto Sites Builder
 * Build 060 - Accessibility CRITICAL FIX (aria-hidden Warning)
 * 
 * Initializes GLightbox for product images in battery tables
 * Custom styles in frontend.css:
 * - Overlay: rgba(0, 75, 97, 0.70)
 * - Close Button: CSS-based X-icon with hover effect
 * - Navigation: Hidden (only 1 image per battery)
 * 
 * Accessibility (Build 060 Fix):
 * - CRITICAL: document.activeElement.blur() explizit aufgerufen
 * - Click Event Listener für zusätzliche Absicherung
 * - Focus management: Close button receives focus (50ms delay)
 * - Keyboard navigation: Focus outline visible (orange)
 * - Screen readers: aria-hidden warning NOW FIXED
 */

document.addEventListener('DOMContentLoaded', function() {
	// Check if GLightbox is available
	if (typeof GLightbox !== 'undefined') {
		// Initialize GLightbox for product images
		const lightbox = GLightbox({
			selector: '.glightbox',
			touchNavigation: true,
			loop: false,
			autoplayVideos: false,
			closeButton: true,
			closeOnOutsideClick: true,
			skin: 'clean',
			zoomable: true,
			draggable: true,
			dragToleranceX: 40,
			dragToleranceY: 65,
			preload: true,
			// SVGs werden durch CSS-Pseudo-Elemente ersetzt (siehe frontend.css)
			svg: {
				close: '', // Leer, da wir CSS ::before/::after verwenden
				next: '',  // Ausgeblendet via CSS
				prev: ''   // Ausgeblendet via CSS
			},
			// Event Handlers für bessere Accessibility
			onOpen: function() {
				// CRITICAL: Blur den aktuell fokussierten Element (der angeklickte Link)
				// um aria-hidden Warning zu vermeiden
				if (document.activeElement && document.activeElement.blur) {
					document.activeElement.blur();
				}
				
				// Focus auf Close Button verschieben
				setTimeout(function() {
					const closeButton = document.querySelector('.gclose');
					if (closeButton) {
						closeButton.focus();
					}
				}, 50);
			}
		});
		
		// Alternative Lösung: Click Event Listener auf alle glightbox Links
		// Entfernt Focus VOR dem Öffnen der Lightbox
		document.querySelectorAll('.glightbox').forEach(function(link) {
			link.addEventListener('click', function(e) {
				// Blur nach kurzem Delay damit Click-Event durchkommt
				setTimeout(function() {
					if (document.activeElement) {
						document.activeElement.blur();
					}
				}, 10);
			});
		});
		
		console.log('Ayonto GLightbox initialized');
	} else {
		console.warn('GLightbox library not loaded');
	}
});
