(function($) {

'use strict';

$(function() {

	var color = $('#flair_color'),
		icon = $('#flair_icon'),
		iconColor = $('#flair_icon_color'),
		fontColor = $('#flair_font_color'),
		preview = $('#flair_preview');

	/**
	 * Get the HTML for a flair preview.
	 *
	 * @param {String}	colorVal		The background color
	 * @param {String}	iconVal			The icon name
	 * @param {String}	iconColorVal	The icon color
	 * @param {String}	fontColorVal	The font color
	 *
	 * @return {String} The HTML
	 */
	var getPreviewHtml = function(colorVal, iconVal, iconColorVal, fontColorVal) {
		var html = '<span class="fa-stack">';

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
			html += '<i class="fa fa-stack-2x" style="color: #' + fontColorVal + '">2</i>';
		}

		html += '</span>';

		return html;
	};

	/**
	 * Update the flair preview based on the current values of the form fields.
	 */
	var updatePreview = function() {
		var colorVal = color.val(),
			iconVal = icon.val(),
			iconColorVal = iconColor.val(),
			fontColorVal = fontColor.val();

		var html = getPreviewHtml(colorVal, iconVal, iconColorVal);

		if (fontColorVal) {
			html += getPreviewHtml(colorVal, iconVal, iconColorVal, fontColorVal);
		}

		preview.html(html);
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
