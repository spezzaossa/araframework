jQuery(document).ready(function() {

	updateMenu();
	updateElements();

	jQuery("#new_menu").click(function(e) {
		var label = jQuery('input[name="label"]').val();
		var link = jQuery('input[name="link"]').val();

		jQuery.ajax({
			type: 'POST',
			url: "admin-roles-menu-create",
			dataType: 'json',
			success: function(e) {
				jQuery('input[name="label"]').val('');
				jQuery('input[name="link"]').val('');
				updateElements();
			},
			data: {
				'label': label,
				'link': link
			}
		});
		return false;
	});
});

function updateElements()
{
	jQuery.ajax({
		type: 'POST',
		url: "admin-roles-menu-all-"+jQuery('#all-menus').data('id'),
		dataType: 'json',
		success: function(e) {
			jQuery('#all-menus').html(e);
			jQuery('.menu').draggable({revert: true});
			jQuery('.trash').droppable({
				accept: '.menu-entry',
				activate: function(event, ui) {
					jQuery(this).toggleClass('highlight');
				},
				deactivate: function(event, ui) {
					jQuery(this).toggleClass('highlight');
				},
				drop: function (event, ui) {
					var menu_id = ui.draggable.data('id');

					jQuery.ajax({
						type: 'POST',
						url: "admin-roles-menu-delete-"+jQuery('#menu_preview').data('id'),
						dataType: 'json',
						success: function(e) {
							updateElements();
							updateMenu();
						},
						data: {
							'menu': menu_id
						}
					});
				}
			});
		},
		data: {
		}
	});
}

function updateMenu()
{
	jQuery.ajax({
		type: 'POST',
		url: "admin-roles-menu-get-"+jQuery('#menu_preview').data('id'),
		dataType: 'json',
		success: function(e) {
			jQuery('#menu_preview').html(e);

			jQuery('.receiver').droppable({
				accept: '.menu',
				activate: function(event, ui) {
					jQuery(this).toggleClass('highlight');
				},
				deactivate: function(event, ui) {
					jQuery(this).toggleClass('highlight');
				},
				drop: function(event, ui) {
					var element_id = ui.draggable.data('id');
					var parent_id = jQuery(this).data('parent');

					jQuery.ajax({
						type: 'POST',
						url: "admin-roles-menu-add-"+jQuery('#menu_preview').data('id'),
						dataType: 'json',
						success: function(e) {
							updateElements();
							updateMenu();
						},
						data: {
							'element': element_id,
							'parent': parent_id
						}
					});
				}
			});

			jQuery('ul.dropdown-menu').sortable({
				stop: function(event, ui) {
					var entries = jQuery(this).find('.menu-entry');
					var ids = new Array();

					for (var i = 0; i < entries.length; i++)
					{
						ids.push(jQuery(entries[i]).data('id'));
					}
					jQuery.ajax({
						type: 'POST',
						url: "admin-roles-menu-edit-"+jQuery('#menu_preview').data('id'),
						dataType: 'json',
						success: function(e) {
							updateMenu();
						},
						data: {
							'elements': ids.join(',')
						}
					});
				}
			});

			jQuery('ul.editable').sortable({
				stop: function(event, ui) {
					var entries = jQuery(this).find('.dropdown.menu-entry');
					var ids = new Array();

					for (var i = 0; i < entries.length; i++)
					{
						ids.push(jQuery(entries[i]).data('id'));
					}
					jQuery.ajax({
						type: 'POST',
						url: "admin-roles-menu-edit-"+jQuery('#menu_preview').data('id'),
						dataType: 'json',
						success: function(e) {
							updateMenu();
						},
						data: {
							'elements': ids.join(',')
						}
					});
				}
			});
		},
		data: {
		}
	});
}