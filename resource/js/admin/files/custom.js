var oTable;

jQuery(document).ready(function() {

	oTable = jQuery("#files").dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "admin-files",
		"bSort": true,
		"sScrollY":	"300px",
		"aaSorting": [[3, "desc"]],
		"aoColumns": [
			{"sWidth": "5%", "bSortable": false},
			null,
			{"sWidth": "20%"},
			{"sWidth": "20%"},
			{"sWidth": "15%", "sClass": "operazioni", "bSortable": false}
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
				if (!confirm('Sei veramente sicuro di voler rimuovere questo file?')) return false;
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery.ajax({
					type: 'POST',
					url: "admin-files-delete",
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

			jQuery("td.operazioni .clone").click(function(e) {
				e.preventDefault();
				var id = jQuery(this).parent().attr('id').substring(4);
				jQuery.ajax({
					type: 'POST',
					url: "admin-files-clone",
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

	var uploader = new qq.FineUploader({
//		validation: {
//			allowedExtensions: ['jpeg', 'jpg']
//		},
//		showMessage: function(message) {
//			jQuery('#profile-image .upload-error').show().delay(5000).fadeOut();
//		},
//		multiple: false,
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: document.getElementById('file-uploader'),
		request: {
			// path to server-side upload script
			endpoint: "admin-files-upload"
		},
		text: {
			uploadButton: uploadButtonText
		},
		callbacks: {
//					onSubmit: function(id, fileName){ alert('invio '+fileName) },
			onComplete: function(id, fileName, responseJSON){
				oTable.fnDraw();
			},
			onError: function(id, fileName, xhr){

			}
		}
	});

	qqFileUploaderBootstrapIntegration();

});
