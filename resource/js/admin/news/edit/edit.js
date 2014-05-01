var oTableFiles;
var oTableGalleries;

function dataTablesRefresh()
{
	oTableFiles.fnAdjustColumnSizing(false);
	oTableGalleries.fnAdjustColumnSizing(false);
}

function initAttachments()
{
	jQuery('#attachments').sortable();

	jQuery('#attachments .glyphicon-trash').unbind('click').click(function() {
		jQuery(this).parent().parent().parent().remove();
	});
}

jQuery(document).ready(function() {
	var content = jQuery('textarea.content');

	CKEDITOR.config.customConfig = '../js/admin.ckeditor.config.js';
	CKEDITOR.replace(content.get(0));

	jQuery('#update-slug').click(function() {
		jQuery('.form-group.slug')
			.removeClass('has-error')
			.removeClass('has-warning')
			.removeClass('has-success');
		jQuery('.form-group.slug .help-block').html('');

		var candidate = convertToSlug(jQuery('input[name="title"]').val());
		var slug_id = jQuery('input[name="slug"]').data('id');

		if (!proposeSlug(candidate, slug_id))
		{
			candidate = convertToSlug(candidate + ' ' + jQuery('input[name="date"]').val());
			if (!proposeSlug(candidate, slug_id))
			{
				jQuery('.form-group.slug').addClass('has-warning');
				jQuery('.form-group.slug .help-block').html('Non è stato possibile individuare automaticamente un URL.');
				candidate = '';
			}
		}

		if (candidate) jQuery('.form-group.slug .help-block').html('');

		jQuery('input[name="slug"]').val(candidate);
		jQuery('#seo_panel').collapse('show');
		jQuery('input[name="slug"]').focus();
		jQuery('html, body').animate({
			scrollTop: jQuery('input[name="slug"]').offset().top
		}, 1000);
	});

	jQuery('input[name="slug"]').keyup(function(){
		jQuery('.form-group.slug')
			.removeClass('has-error')
			.removeClass('has-warning')
			.removeClass('has-success');
		jQuery('.form-group.slug .help-block').html('');

		jQuery(this).doTimeout('text-type', 500, function(){
			if (jQuery(this).val() != '')
			{
				jQuery(this).val(convertToSlug(jQuery(this).val()));
				if (!proposeSlug(jQuery(this).val(), jQuery('input[name="slug"]').data('id')))
				{
					jQuery('.form-group.slug').addClass('has-error');
					jQuery('.form-group.slug .help-block').html('L\'URL selezionato &egrave; gi&agrave; in uso. Usarne uno differente.');
				}
				else
				{
					jQuery('.form-group.slug').addClass('has-success');
					jQuery('.form-group.slug .help-block').html('Questo URL pu&ograve; essere utilizzato.');
				}
			}
		});
	});

	jQuery(window).resize(function() {
		dataTablesRefresh();
	});

	jQuery('.collapse').on('shown.bs.collapse', function() {
		dataTablesRefresh();
	});

//	jQuery('.collapse').collapse();

	jQuery('#gallery .glyphicon-trash').click(function() {
		jQuery(this).parent().html('Nessuna');
	});

	initAttachments();

	jQuery('form').submit(function(e){
		var attachments = new Array();
		jQuery('#attachments .attachment').each(function(i,v) {
			attachments.push(jQuery(v).data('id'));
		});

		jQuery('input[name="attachments"]').val(attachments.join(','));
	});

	oTableFiles = jQuery("#files").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-news-files",
		"bSort": true,
		"sScrollY":	"300px",
		"aaSorting": [[3, "desc"]],
		"aoColumns": [
			{"sWidth": "5%", "bSortable": false},
			null,
			{"sWidth": "20%"},
			{"sWidth": "20%"},
			{"sWidth": "10%", "sClass": "operazioni", "bSortable": false}
		],
		"oLanguage": {
			"sSearch": "", // Il testo è nello script dell'integrazione con Bootstrap
			"sInfo": "_TOTAL_ risultati (visualizzati da _START_ a _END_)",
			"sLengthMenu": "Mostra _MENU_ risultati",
			"sZeroRecords": "Nessun dato da mostrare",
			"sInfoFiltered": " - filtrato da _MAX_ righe"
		},
		"fnDrawCallback": function() {
			jQuery("td.operazioni .attach").click(function(e) {
				var elem = jQuery('#prototypes .attachment').clone();
				elem.data('id', jQuery(this).data('id'));
				elem.find('span.filename').html(jQuery(this).data('filename'));
				elem.appendTo('#attachments');

				initAttachments();
			});
		}
	});

	jQuery('#gallery .glyphicon-trash').click(function() {
		jQuery(this).parent().html('Nessuna');
		jQuery('input[name="gallery"]').val('0');
	});

	oTableGalleries = jQuery("#galleries").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-news-galleries",
		"bSort": true,
		"sScrollY":	"300px",
		"aaSorting": [[0, "asc"]],
		"aoColumns": [
			null,
			{"sWidth": "10%", "bSortable": false},
			{"sWidth": "10%"},
			{"sWidth": "10%", "sClass": "operazioni", "bSortable": false}
		],
		"oLanguage": {
			"sSearch": "", // Il testo è nello script dell'integrazione con Bootstrap
			"sInfo": "_TOTAL_ risultati (visualizzati da _START_ a _END_)",
			"sLengthMenu": "Mostra _MENU_ risultati",
			"sZeroRecords": "Nessun dato da mostrare",
			"sInfoFiltered": " - filtrato da _MAX_ righe"
		},
		"fnDrawCallback": function() {
			jQuery("td.operazioni .select").click(function(e) {
				jQuery('input[name="gallery"]').val(jQuery(this).data('id'));
				jQuery('#gallery').html(jQuery(this).data('name')+' <span class="glyphicon glyphicon-trash" title="Rimuovi"></span>');
				jQuery('#gallery .glyphicon-trash').click(function() {
					jQuery(this).parent().html('Nessuna');
					jQuery('input[name="gallery"]').val('0');
				});
			});
		}
	});

	dataTablesBootstrapIntegration();
});