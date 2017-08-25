(function($) {

'use strict';

$(function() {

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
});

}(jQuery));
