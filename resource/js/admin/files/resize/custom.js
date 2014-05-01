
jQuery(document).ready(function() {
	jQuery('input[name="width"]').bind('propertychange keyup input paste', function() {
		if (jQuery('#proporzioni:checked').length)
		{
			var w = parseInt(jQuery(this).val());
			var h = w / ratio;
			if (w) jQuery('input[name="height"]').val(h.toFixed(0));
		}
	});

	jQuery('input[name="height"]').bind('propertychange keyup input paste', function() {
		if (jQuery('#proporzioni:checked').length)
		{
			var h = parseInt(jQuery(this).val());
			var w = h * ratio;
			if (h) jQuery('input[name="width"]').val(w.toFixed(0));
		}
	});
});
