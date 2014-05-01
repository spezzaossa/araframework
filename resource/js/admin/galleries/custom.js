var oTable;

jQuery(document).ready(function() {

	oTable = jQuery("#galleries").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-galleries",
		"bSort": true,
		"bAutoWidth": false,
		"aaSorting": [[0, "asc"]],
		"aoColumns": [
			null,
			{"sWidth": "10%", "bSortable": false},
			{"sWidth": "10%"},
			{"sWidth": "10%"},
			{"sWidth": "10%"},
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
				if (!confirm('Sei veramente sicuro di voler rimuovere questa galleria?')) return false;
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery.ajax({
					type: 'POST',
					url: "admin-galleries-delete",
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
