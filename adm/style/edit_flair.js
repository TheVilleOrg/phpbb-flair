(function($) {

'use strict';

$(function() {

	var color		= $('#flair_color'),
		icon		= $('#flair_icon'),
		iconColor	= $('#flair_icon_color'),
		fontColor	= $('#flair_font_color'),
		preview		= $('#flair_preview');

	var getColorValue = (function() {
		var colorRegEx = new RegExp('^[0-9A-F]{6}$');

		/**
		 * Get the value from an input field only if it is a valid color hex value. If the field is
		 * not empty and the value is invalid, this function will also apply the error class to the
		 * field.
		 *
		 * @param {JQuery}	field	The field from which to get the value
		 *
		 * @return {String} The value or an empty string if it is invalid
		 */
		return function(field) {
			var value = field.val();

			field.removeClass('error');

			if (value) {
				if (colorRegEx.test(value)) {
					return value;
				}

				field.addClass('error');
			}

			return '';
		};
	}());

	/**
	 * Get the HTML for a flair preview.
	 *
	 * @param {String}	colorVal		The background color
	 * @param {String}	iconVal			The icon name
	 * @param {String}	iconColorVal	The icon color
	 * @param {String}	fontColorVal	The font color
	 * @param {Boolean}	large			Get the larger preview
	 *
	 * @return {String} The HTML
	 */
	var getPreviewHtml = function(colorVal, iconVal, iconColorVal, fontColorVal, large) {
		var html = '<span class="fa-stack';

		if (large) {
			html += ' fa-2x';
		}

		html += '">';

		if (colorVal) {
			html += '<i class="fa fa-square fa-stack-2x" style="color: #' + colorVal + '"></i>';

			if (iconVal) {
				html += '<i class="fa ' + iconVal + ' fa-stack-1x"';

				if (iconColorVal) {
					html += ' style="color: #' + iconColorVal + '"';
				}

				html += '></i>';
			}
		} else if (iconVal) {
			html += '<i class="fa ' + iconVal + ' fa-stack-2x"';

			if (iconColorVal) {
				html += ' style="color: #' + iconColorVal + '"';
			}

			html += '></i>';
		}

		if (fontColorVal) {
			html += '<b class="flair-count" style="color: #' + fontColorVal + '">2</b>';
		}

		html += '</span>';

		return html;
	};

	/**
	 * Update the flair preview based on the current values of the form fields.
	 */
	var updatePreview = function() {
		var colorVal		= getColorValue(color),
			iconVal			= icon.val(),
			iconColorVal	= getColorValue(iconColor),
			fontColorVal	= getColorValue(fontColor);

		if (!colorVal && !iconVal) {
			preview.html('');
			return;
		}

		var html = [];

		html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, false, true));
		html.push(getPreviewHtml(colorVal, iconVal, iconColorVal));

		if (fontColorVal) {
			html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, fontColorVal, true));
			html.push(getPreviewHtml(colorVal, iconVal, iconColorVal, fontColorVal));
		}

		preview.html(html.join('&nbsp;'));
	};

	$('#color_palette_toggle1').click(function(e) {
		$('#color_palette_placeholder').toggle();
		e.preventDefault();
	});

	var palette2 = $('#color_palette_placeholder2');
	phpbb.registerPalette(palette2);
	$('#color_palette_toggle2').click(function(e) {
		palette2.toggle();
		e.preventDefault();
	});

	var palette3 = $('#color_palette_placeholder3');
	phpbb.registerPalette(palette3);
	$('#color_palette_toggle3').click(function(e) {
		palette3.toggle();
		e.preventDefault();
	});

	$('.colour-palette a').click(function() {
		var colorVal = $(this).data('color'),
			target = $($(this).parents('.color_palette_placeholder').data('target'));
		target.val(colorVal);
		updatePreview();
	});

	$('#flair_color, #flair_icon, #flair_icon_color, #flair_font_color').change(function() {
		updatePreview();
	});

});

}(jQuery));
