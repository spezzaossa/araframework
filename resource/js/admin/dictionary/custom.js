var oTable;

jQuery(document).ready(function() {

	jQuery('#new_entry').click(function() {
		jQuery('.dictionary-add').removeClass('has-error');
		jQuery.ajax({
			type: 'POST',
			url: "admin-dictionary-create",
			dataType: 'json',
			success: function(e) {
				jQuery("#dictionary").dataTable().fnDraw(false);
				if(e == 1) {
					jQuery('input[name="name"]').val('');
					jQuery('input[name="value"]').val('');
				} else {
					jQuery('.dictionary-add').addClass('has-error');
				}
			},
			data: {
				'name': jQuery('input[name="name"]').val(),
				'value': jQuery('input[name="value"]').val(),
				'lang': jQuery('select[name="lang"]').val()
			}
		});
	});

	jQuery('select[name="table_lang"]').change(function() {
		oTable.fnReloadAjax("admin-dictionary-"+jQuery(this).val());
	});

	oTable = jQuery("#dictionary").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-dictionary-1",
		"bSort": true,
		"sScrollY":	"300px",
		"aoColumns": [
			{"sWidth": "10px"},
			{"sWidth": "20%"},
			null,
			{"sWidth": "10%", "sClass": "operazioni"}
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
			jQuery("td.operazioni .save").click(function(e) {
				e.preventDefault();
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery("#row_"+id+" .save").hide();
				jQuery("#row_"+id+" .cancel").hide();
				jQuery("#row_"+id+" .loading").show();
				jQuery.ajax({
					type: 'POST',
					url: "admin-dictionary-save",
					dataType: 'json',
					success: function(e) {
						if(e == 1) {
							jQuery('#input_'+id).attr('original', jQuery('#input_'+id).val());
						} else {
							jQuery('#input_'+id).val(jQuery('#input_'+id).attr('original'));
						}
						jQuery("#row_"+id+" .save").show();
						jQuery("#row_"+id+" .cancel").show();
						jQuery("#row_"+id+" .loading").hide();
					},
					data: {
						'id': id,
						'value': jQuery('#input_'+id).val()
					}
				});
				return false;
			});
			jQuery("td.operazioni .cancel").click(function(e) {
				e.preventDefault();
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery('#input_'+id).val(jQuery('#input_'+id).attr('original'));
				return false;
			});
		}
	});

	dataTablesBootstrapIntegration();
});
