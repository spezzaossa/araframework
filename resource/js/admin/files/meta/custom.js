var oTable;

jQuery(document).ready(function() {

	jQuery('#new_meta').click(function() {
		jQuery('.meta-add').removeClass('has-error');
		jQuery.ajax({
			type: 'POST',
			url: document.location.pathname + "-edit",
			dataType: 'json',
			success: function(e) {
				jQuery("#meta").dataTable().fnDraw(false);
				if(e == 1) {
					jQuery('input[name="title"]').val('');
					jQuery('input[name="alt"]').val('');
				} else {
					jQuery('.meta-add').addClass('has-error');
				}
			},
			data: {
				'title': jQuery('input[name="title"]').val(),
				'alt': jQuery('input[name="alt"]').val(),
				'lang': jQuery('select[name="lang"]').val()
			}
		});
	});

	oTable = jQuery("#meta").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "",
		"bSort": true,
		"sScrollY":	"300px",
		"aoColumns": [
			null,
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
			jQuery("td.operazioni .delete").click(function(e) {
				e.preventDefault();
				if (!confirm('Sei veramente sicuro di voler rimuovere questi attributi?')) return false;
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery.ajax({
					type: 'POST',
					url: document.location.pathname + "-delete",
					dataType: 'json',
					success: function(e) {
						oTable.fnDraw();
					},
					data: {
						'id': id
					}
				});
				return false;
			});
		}
	});

	dataTablesBootstrapIntegration();
});
