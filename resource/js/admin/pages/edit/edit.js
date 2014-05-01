jQuery(document).ready(function() {
	CKEDITOR.config.customConfig = '../js/admin.ckeditor.config.js';
	CKEDITOR.replaceAll('content');

	jQuery('select[name="table_lang"]').change(function() {
		var id = jQuery(this).val();
		jQuery('.tab').hide();
		jQuery('#tabs-'+id).show();
	});

	var id = jQuery('select[name="table_lang"]').val();
	jQuery('.tab').hide();
	jQuery('#tabs-'+id).show();
});