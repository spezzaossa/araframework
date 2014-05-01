jQuery(document).ready(function() {
	CKEDITOR.config.customConfig = '../js/admin.ckeditor.config.js';
	CKEDITOR.replaceAll('content');

	jQuery('.save').button();
	jQuery('.save').click(function() {
		var textarea = jQuery(this).parent().find('textarea');
		var content = CKEDITOR.instances[textarea.attr('name')].getData();
//		jAlert('<img src="resource/img/loading.gif" />Caricamento');
//TODO: mettere alert
		jQuery.ajax({
			type: 'POST',
			url: "admin-page-save",
			dataType: 'json',
			success: function(e) {
//				if (e == 1)
//					jAlert('Modifica salvata correttamente');
//				else
//					jAlert('Errore nel salvataggio');
			},
			data: {
				'id': textarea.attr('content'),
				'title': textarea.parent().find('input[name="title"]').val(),
				'value': content
			}
		});
	});

	jQuery('select[name="table_lang"]').change(function() {
		var id = jQuery(this).val();
		jQuery('.tab').hide();
		jQuery('#tabs-'+id).show();
	});

	var id = jQuery('select[name="table_lang"]').val();
	jQuery('.tab').hide();
	jQuery('#tabs-'+id).show();
});