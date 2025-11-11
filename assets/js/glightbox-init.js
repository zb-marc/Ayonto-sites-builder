/**
 * GLightbox Initialization for Voltrana Sites Builder
 * Build 055
 * 
 * Initializes GLightbox for product images in battery tables
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
			svg: {
				close: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="#fff" d="M15 5L5 15M5 5l10 10"/></svg>',
				next: '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25"><path fill="#fff" d="M9 6l6 6-6 6"/></svg>',
				prev: '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25"><path fill="#fff" d="M16 6l-6 6 6 6"/></svg>'
			}
		});
		
		console.log('Voltrana GLightbox initialized');
	} else {
		console.warn('GLightbox library not loaded');
	}
});
