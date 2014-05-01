var crop;

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

	jQuery('#proporzioni').click(function() {
		crop.destroy();
		removeCrop();
		
		if (jQuery('#proporzioni:checked').length)
		{
			console.log('1');
			jQuery('#thumb').Jcrop({
				boxHeight: 400,
				onChange: updateCrop,
				onSelect: updateCrop,
				onRelease: removeCrop,
				aspectRatio: ratio
			}, function () {
				crop = this;
			});
		}
		else
		{
			console.log('2');
			jQuery('#thumb').Jcrop({
				boxHeight: 400,
				onChange: updateCrop,
				onSelect: updateCrop,
				onRelease: removeCrop
			}, function () {
				crop = this;
			});
		}
	});

	jQuery('#thumb').Jcrop({
		boxHeight: 400,
		onChange: updateCrop,
		onSelect: updateCrop,
		onRelease: removeCrop,
		aspectRatio: ratio
	}, function () {
		crop = this;
	});
});

function updateCrop(coords)
{
	jQuery('#x1').val(coords.x);
	jQuery('#y1').val(coords.y);
	jQuery('#x2').val(coords.x2);
	jQuery('#y2').val(coords.y2);
	jQuery('#w').val(coords.w);
	jQuery('#h').val(coords.h);
}

function removeCrop()
{
	jQuery('#x1').val(0);
	jQuery('#y1').val(0);
	jQuery('#x2').val(0);
	jQuery('#y2').val(0);
	jQuery('#w').val(0);
	jQuery('#h').val(0);
}