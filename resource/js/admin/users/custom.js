var oTable;

jQuery(document).ready(function() {

	jQuery('#new_user').click(function() {
		jQuery.ajax({
			type: 'POST',
			url: "admin-users-create",
			dataType: 'json',
			success: function(e) {
				jQuery("#users").dataTable().fnDraw(false);
				jQuery('input[name="name"]').val('');
				jQuery('input[name="password"]').val('');
			},
			data: {
				'name': jQuery('input[name="name"]').val(),
				'password': jQuery('input[name="password"]').val(),
				'role': jQuery('select[name="role"]').val()
			}
		});
	});

	oTable = jQuery("#users").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-users",
		"bSort": true,
		"sScrollY":	"300px",
		"aoColumns": [
			{"sWidth": "10%"},
			null,
			{"sWidth": "10%", "bSortable": false},
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
				if (confirm('Sei sicuro di voler cancellare questo amministratore?')) {
					e.preventDefault();
					var id = jQuery(this).parent().attr('id').substring(4);
					jQuery("#row_"+id+" .delete").hide();
					jQuery("#row_"+id+" .psw").hide();
					jQuery.ajax({
						type: 'POST',
						url: "admin-users-delete-"+id,
						dataType: 'json',
						success: function(e) {
							jQuery("#users").dataTable().fnDraw(false);
						},
						data: {

						}
					});
					return false;
				}
			});
			jQuery("td.operazioni .psw").click(function(e) {
				var psw = prompt('Inserisci la nuova password');
				if (psw) {
					e.preventDefault();
					var id = jQuery(this).parent().attr('id').substring(4);
					jQuery("#row_"+id+" .delete").hide();
					jQuery("#row_"+id+" .psw").hide();
					jQuery.ajax({
						type: 'POST',
						url: "admin-users-password-"+id,
						dataType: 'json',
						success: function(e) {
							alert('Password modificata')
							jQuery("#row_"+id+" .delete").show();
							jQuery("#row_"+id+" .psw").show();
						},
						data: {
							'password': psw
						}
					});
					return false;
				}
			});
		}
	});
});
