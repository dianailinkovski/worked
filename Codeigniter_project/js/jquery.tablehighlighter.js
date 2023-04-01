/**
 * jquery.table_highlight.php
 *
 * Add the click_to_highlight function to the jQuery Library
 */

(function($) {

	/**
	 * Highlights a column in a table when clicked
	 */
	$.fn.click_to_highlight = function() {
		var hclass = 'datahighlight';
		var headers = $(this).children('thead').children('tr').children('th.header');
		var rows = $(this).children('tbody').children();
		$(this).after('<div id="jqxtooltip"></div>');
		$('#jqxtooltip').jqxTooltip({
			width: 100
		});

		// add the highlighter option to header
		headers.mouseenter(function() {
			$(this).append('<span class="highlightIcon"></span>');
			var header = $(this);


			// add the tooltip to the span button
			$('#jqxtooltip').jqxTooltip('add', $('span.highlightIcon'), 'Toggle Highlight');

			// on click, highlight/unhighlight the column
			$('span.highlightIcon').click(function(event) {
				event.stopPropagation();
				var remove = header.attr('data-selected') == 1;

				headers.removeAttr('data-selected');

				rows.children().removeClass(hclass);

				if ( ! remove) {
					header.attr('data-selected', '1');
					var index = header.prevAll().length;
					rows.find('td:nth-child(' + (index + 1) + ')').addClass(hclass);
				}
			});


		});

		// remove the highlighter option from header
		headers.mouseleave(function() {
			$('span.highlightIcon').remove();
		});
	};

})(jQuery);
