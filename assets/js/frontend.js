/* Ayonto Sites Builder - Frontend Scripts */
(function($) {
	'use strict';
	
	$(document).ready(function() {
		// Build 079: Clean up empty paragraphs and breaks around battery tables
		// This helps prevent gaps especially on mobile devices
		$('.vt-battery-table-wrapper').each(function() {
			var $wrapper = $(this);
			
			// Remove empty paragraphs immediately after the wrapper
			var $nextElement = $wrapper.next();
			while ($nextElement.length && 
				   ($nextElement.is('p:empty') || 
				    $nextElement.is('br') || 
				    ($nextElement.is('p') && $nextElement.html().trim() === ''))) {
				$nextElement.remove();
				$nextElement = $wrapper.next();
			}
			
			// Remove empty paragraphs immediately before the wrapper
			var $prevElement = $wrapper.prev();
			while ($prevElement.length && 
				   ($prevElement.is('p:empty') || 
				    $prevElement.is('br') || 
				    ($prevElement.is('p') && $prevElement.html().trim() === ''))) {
				$prevElement.remove();
				$prevElement = $wrapper.prev();
			}
			
			// For mobile: ensure no margin on the wrapper itself
			if (window.innerWidth <= 767) {
				$wrapper.css({
					'margin': '0',
					'padding': '0'
				});
				
				// Also ensure last row has no margin
				$wrapper.find('tbody tr:last-child').css('margin-bottom', '0');
			}
		});
		
		// Additional frontend functionality can be added here
	});
})(jQuery);
