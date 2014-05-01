jQuery(document).ready(function () {

	init();

	jQuery('.gallery').sortable({
		tolerance: "pointer",
		items: ".element",
		receive: function(event, ui) {
			updateImages();
		}
	});

	jQuery('.gallery .thumbnail .glyphicon-trash').click(function() {
		jQuery(this).parent().parent().parent().remove();
	});

	jQuery('.save').click(function() {
		images = Array();

		jQuery('.gallery .thumbnail').each(function(i, v){
			images.push(jQuery(v).data('image'));
		});

		jQuery.ajax({
			type: 'POST',
			url: document.location.pathname + "-save",
			dataType: 'json',
			success: function(data) {
				alert('Galleria salvata!');
			},
			data: {
				'images': images
			}
		});
	});
});

function init()
{
	jQuery('.thumbnail').tooltip();

	jQuery('.images .element').draggable({
		connectToSortable: ".gallery",
		cursor: "move",
		helper: "clone"
	});

	jQuery('.images .thumbnail .glyphicon-plus').click(function() {
		jQuery('.gallery').append(jQuery(this).parent().parent().parent().clone());
		jQuery('.gallery .tooltip').remove();

		updateImages();
	});

	jQuery('.pagination li').click(function() {
		jQuery.ajax({
			type: 'POST',
			url: "",
			dataType: 'json',
			success: function(data) {
				jQuery('.images').parent().html(data);
				init();
			},
			data: {
				'page_number': jQuery(this).data('page')
			}
		});
	});
}

function updateImages()
{
	jQuery('.gallery .thumbnail').tooltip();
	jQuery('.gallery .thumbnail .caption span').removeClass('glyphicon-plus').addClass('glyphicon-trash');

	jQuery('.gallery .thumbnail .glyphicon-trash').unbind('click').click(function() {
		jQuery(this).parent().parent().parent().remove();
	});
}
