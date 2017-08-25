(function($) {

'use strict';

$(function() {

	var color = $('#flair_color'),
		icon = $('#flair_icon'),
		iconColor = $('#flair_icon_color'),
		preview = $('#flair_preview');

	var updatePreview = function() {
		var colorVal = color.val(),
			iconVal = icon.val(),
			iconColorVal = iconColor.val();

		var html = '';
		var size = 'fa-2x';

		if (colorVal) {
			size = 'fa-stack-1x';
			html += '<span class="fa-stack">';
			html += '<i class="fa fa-square fa-stack-2x" style="color: #' + colorVal + '"></i>';
		}

		if (iconVal) {
			html += '<i class="fa ' + iconVal + ' ' + size + '"';
			if (iconColorVal) {
				html += ' style="color: #' + iconColorVal + '"';
			}
			html += '></i>';
		}

		if (colorVal) {
			html += '</span>';
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

	$('.colour-palette a').click(function() {
		var colorVal = $(this).data('color'),
			target = $($(this).parents('#color_palette_placeholder, #color_palette_placeholder2').data('target'));
		target.val(colorVal);
		updatePreview();
	});

	$('#flair_color, #flair_icon, #flair_icon_color').change(function() {
		updatePreview();
	});

});

}(jQuery));
