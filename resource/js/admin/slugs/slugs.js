var oTable;

jQuery(document).ready(function() {

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
				if (!proposeSlug(jQuery(this).val(), jQuery('input[name="id"]').val()))
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

	oTable = jQuery("#slugs").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-slugs",
		"bSort": true,
		"sScrollY":	"300px",
//		"aaSorting": [[0, "desc"]],
		"aoColumns": [
			null,
			null,
			{"sWidth": "10%", "sClass": "operazioni", "bSortable": false}
		],
		"oLanguage": {
//			"sSearch": "Cerca in tutte le colonne:",
			"sSearch": "", // Il testo è nello script dell'integrazione con Bootstrap
			"sInfo": "_TOTAL_ risultati (visualizzati da _START_ a _END_)",
			"sLengthMenu": "Mostra _MENU_ risultati",
			"sZeroRecords": "Nessun dato da mostrare",
			"sInfoFiltered": " - filtrato da _MAX_ righe"
		},
		"fnDrawCallback": function() {
			jQuery("td.operazioni .edit").click(function(e) {
				jQuery('input[name="id"]').val(jQuery(this).data('id'));
				jQuery('input[name="slug"]').val(jQuery(this).data('slug'));
				jQuery('input[name="page_url"]').val(jQuery(this).data('url'));
				jQuery('input[name="slug"]').focus();
				jQuery('html, body').animate({
					scrollTop: jQuery('input[name="slug"]').offset().top
				}, 1000);
			});

			jQuery("td.operazioni .delete").click(function(e) {
				e.preventDefault();
				if (!confirm('Sei veramente sicuro di voler rimuovere questo URL personalizzato?')) return false;

				jQuery('input[name="id"]').val('');
				jQuery('input[name="slug"]').val('');
				jQuery('input[name="page_url"]').val('');

				jQuery.ajax({
					type: 'POST',
					url: "admin-slugs-delete",
					dataType: 'json',
					success: function(e) {
						oTable.fnDraw();
					},
					data: {
						'id': jQuery(this).data('id')
					}
				});
				return false;
			});
		}
	});

	dataTablesBootstrapIntegration();

});
