/**
 * Help & Documentation Page JavaScript
 * @package AyontoSitesBuilder
 * @since 0.1.47
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		/**
		 * Initialize Syntax Highlighting
		 */
		if (typeof hljs !== 'undefined') {
			// Apply syntax highlighting to all code blocks
			$('pre code').each(function(i, block) {
				hljs.highlightElement(block);
			});
		}

		/**
		 * Smooth Scroll for Anchor Links
		 */
		$('.vt-markdown-content a[href^="#"]').on('click', function(e) {
			var target = $(this).attr('href');
			
			if ($(target).length) {
				e.preventDefault();
				
				$('html, body').animate({
					scrollTop: $(target).offset().top - 100
				}, 500);
			}
		});

		/**
		 * Add Copy Button to Code Blocks
		 */
		$('pre code').each(function() {
			var $code = $(this);
			var $pre = $code.parent();
			
			// Skip if button already exists
			if ($pre.find('.vt-copy-btn').length) {
				return;
			}
			
			// Create copy button
			var $copyBtn = $('<button class="vt-copy-btn" title="Code kopieren">' +
				'<span class="dashicons dashicons-admin-page"></span>' +
				'</button>');
			
			$pre.css('position', 'relative').prepend($copyBtn);
			
			// Copy functionality
			$copyBtn.on('click', function(e) {
				e.preventDefault();
				
				var code = $code.text();
				
				// Create temporary textarea
				var $temp = $('<textarea>');
				$('body').append($temp);
				$temp.val(code).select();
				
				try {
					document.execCommand('copy');
					
					// Success feedback
					$copyBtn.addClass('copied');
					$copyBtn.html('<span class="dashicons dashicons-yes"></span>');
					
					setTimeout(function() {
						$copyBtn.removeClass('copied');
						$copyBtn.html('<span class="dashicons dashicons-admin-page"></span>');
					}, 2000);
				} catch(err) {
					console.error('Copy failed:', err);
				}
				
				$temp.remove();
			});
		});

		/**
		 * Table of Contents Generator
		 */
		var generateTOC = function() {
			var $content = $('.vt-markdown-content');
			var $headings = $content.find('h2, h3');
			
			if ($headings.length < 3) {
				return; // Don't show TOC for short documents
			}
			
			var $toc = $('<div class="vt-toc">' +
				'<h4>Inhaltsverzeichnis</h4>' +
				'<ul></ul>' +
				'</div>');
			
			var $tocList = $toc.find('ul');
			
			$headings.each(function(i) {
				var $heading = $(this);
				var id = 'heading-' + i;
				var text = $heading.text();
				var level = parseInt($heading.prop('tagName').substr(1));
				
				// Add ID to heading
				$heading.attr('id', id);
				
				// Create TOC item
				var $item = $('<li class="vt-toc-level-' + level + '">' +
					'<a href="#' + id + '">' + text + '</a>' +
					'</li>');
				
				$tocList.append($item);
			});
			
			// Insert TOC in sidebar after plugin info
			$('.vt-help-info').after($toc);
		};
		
		// Generate TOC if we have content
		if ($('.vt-markdown-content').length) {
			generateTOC();
		}

		/**
		 * TOC Click Handler (smooth scroll)
		 */
		$(document).on('click', '.vt-toc a', function(e) {
			e.preventDefault();
			
			var target = $(this).attr('href');
			
			if ($(target).length) {
				$('html, body').animate({
					scrollTop: $(target).offset().top - 100
				}, 500);
				
				// Update active state
				$('.vt-toc a').removeClass('active');
				$(this).addClass('active');
			}
		});

		/**
		 * Search Input Enhancement
		 */
		var $searchInput = $('.vt-search-input');
		
		if ($searchInput.length) {
			// Clear button
			var $clearBtn = $('<button type="button" class="vt-search-clear" title="Suche löschen">' +
				'<span class="dashicons dashicons-no-alt"></span>' +
				'</button>');
			
			$searchInput.after($clearBtn);
			
			// Show/hide clear button
			var toggleClearButton = function() {
				if ($searchInput.val().length > 0) {
					$clearBtn.show();
				} else {
					$clearBtn.hide();
				}
			};
			
			$searchInput.on('input', toggleClearButton);
			toggleClearButton();
			
			// Clear functionality
			$clearBtn.on('click', function() {
				$searchInput.val('').focus();
				toggleClearButton();
			});
		}

		/**
		 * Keyboard Shortcuts
		 */
		$(document).on('keydown', function(e) {
			// Ctrl/Cmd + K: Focus search
			if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
				e.preventDefault();
				$('.vt-search-input').focus();
			}
			
			// Escape: Clear search focus
			if (e.key === 'Escape') {
				if ($('.vt-search-input').is(':focus')) {
					$('.vt-search-input').blur();
				}
			}
		});

		/**
		 * Scroll Progress Indicator
		 */
		var updateScrollProgress = function() {
			var $content = $('.vt-help-content');
			
			if ($content.length) {
				var scrollTop = $(window).scrollTop();
				var contentTop = $content.offset().top;
				var contentHeight = $content.outerHeight();
				var windowHeight = $(window).height();
				
				var progress = Math.min(100, Math.max(0, 
					((scrollTop - contentTop + windowHeight) / contentHeight) * 100
				));
				
				$('.vt-scroll-progress').css('width', progress + '%');
			}
		};
		
		// Add progress bar
		if ($('.vt-help-content').length) {
			$('body').prepend('<div class="vt-scroll-progress"></div>');
			$(window).on('scroll', updateScrollProgress);
			updateScrollProgress();
		}

		/**
		 * Expand/Collapse Long Code Blocks
		 */
		$('pre code').each(function() {
			var $code = $(this);
			var lineCount = $code.text().split('\n').length;
			
			// If code block has more than 30 lines, make it collapsible
			if (lineCount > 30) {
				var $pre = $code.parent();
				$pre.addClass('vt-code-collapsible collapsed');
				
				var $toggleBtn = $('<button class="vt-code-toggle">' +
					'<span class="expand-text">Code anzeigen (' + lineCount + ' Zeilen)</span>' +
					'<span class="collapse-text">Code ausblenden</span>' +
					'</button>');
				
				$pre.after($toggleBtn);
				
				$toggleBtn.on('click', function() {
					$pre.toggleClass('collapsed');
				});
			}
		});

		/**
		 * External Links in New Tab
		 */
		$('.vt-markdown-content a').each(function() {
			var $link = $(this);
			var href = $link.attr('href');
			
			// External links (not starting with #, /, or same domain)
			if (href && !href.match(/^#/) && !href.match(/^\//) && 
				!href.match(new RegExp('^' + window.location.origin))) {
				$link.attr('target', '_blank');
				$link.attr('rel', 'noopener noreferrer');
				
				// Add external link icon
				$link.append(' <span class="dashicons dashicons-external" style="font-size: 12px; width: 12px; height: 12px; text-decoration: none;"></span>');
			}
		});

		/**
		 * Print Button
		 */
		var $printBtn = $('<button class="vt-print-btn button button-secondary" title="Seite drucken">' +
			'<span class="dashicons dashicons-printer"></span> Drucken' +
			'</button>');
		
		$('.vt-doc-header').append($printBtn);
		
		$printBtn.on('click', function() {
			window.print();
		});

		/**
		 * Dark Mode Toggle (optional - commented out by default)
		 */
		/*
		var $darkModeBtn = $('<button class="vt-dark-mode-btn" title="Dark Mode umschalten">' +
			'<span class="dashicons dashicons-admin-appearance"></span>' +
			'</button>');
		
		$('.vt-help-wrap h1').append($darkModeBtn);
		
		// Check for saved preference
		if (localStorage.getItem('vt-dark-mode') === 'true') {
			$('body').addClass('vt-dark-mode');
		}
		
		$darkModeBtn.on('click', function() {
			$('body').toggleClass('vt-dark-mode');
			localStorage.setItem('vt-dark-mode', $('body').hasClass('vt-dark-mode'));
		});
		*/

		/**
		 * Accessibility: Skip to Content Link
		 */
		var $skipLink = $('<a href="#vt-help-content" class="vt-skip-link screen-reader-text">' +
			'Zum Inhalt springen' +
			'</a>');
		
		$('.vt-help-wrap').prepend($skipLink);
		$('.vt-help-content').attr('id', 'vt-help-content');

	});

})(jQuery);
