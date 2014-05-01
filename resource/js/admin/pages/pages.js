var oTable;

jQuery(document).ready(function() {

	oTable = jQuery("#news").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"bPaginate": false,
		"bAutoWidth": false,
		"sAjaxSource": "admin-pages",
		"bSort": true,
//		"sScrollY":	"300px",
		"aaSorting": [[0, "asc"]],
		"aoColumns": [
			null,
			{"sWidth": "10%", "bSortable": false},
			{"sWidth": "10%", "bSortable": false},
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
				if (!confirm('Sei veramente sicuro di voler rimuovere questa pagina?')) return false;
				jQuery.ajax({
					type: 'POST',
					url: "admin-pages-delete",
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
