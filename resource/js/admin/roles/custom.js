jQuery(document).ready(function() {

	jQuery('#new_role').click(function() {
		jQuery.ajax({
			type: 'POST',
			url: "admin-roles-create",
			dataType: 'json',
			success: function(e) {
				jQuery("#roles").dataTable().fnDraw(false);
				jQuery('input[name="name"]').val('');
			},
			data: {
				'name': jQuery('input[name="name"]').val()
			}
		});
	});

	jQuery("#roles").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-roles",
		"bSort": true,
		"sScrollY":	"300px",
		"aoColumns": [
			{"sWidth": "10%"},
			{"sWidth": "10%", "bSortable": false},
			null,
			{"sWidth": "20%", "sClass": "operazioni", "bSortable": false}
		],
		"oLanguage": {
			"sSearch": "Cerca:",
			"sInfo": "_TOTAL_ risultati (visualizzati da _START_ a _END_)",
			"sLengthMenu": "_MENU_ Risultati per pagina",
			"sZeroRecords": "Nessun dato da mostrare",
			"sInfoFiltered": " - filtrato da _MAX_ righe"
		},
		"fnDrawCallback": function() {
			jQuery("td.operazioni .delete").click(function(e) {
				if (confirm('Sei sicuro di voler cancellare questo ruolo?\nVerrà eliminata la struttura del menù creata per questo ruolo.')) {
					e.preventDefault();
					var id = jQuery(this).parent().attr('id').substring(4);
					jQuery("#row_"+id+" .delete").hide();
					jQuery("#row_"+id+" .menu").hide();
					jQuery.ajax({
						type: 'POST',
						url: "admin-roles-delete-"+id,
						dataType: 'json',
						success: function(e) {
							jQuery("#roles").dataTable().fnDraw(false);
						},
						data: {

						}
					});
					return false;
				}
			});
		}
	});
});
