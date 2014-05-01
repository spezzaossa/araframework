
CKEDITOR.editorConfig = function( config ) {
	config.language = 'it';
	// Elimina i tag P dall'editor
	config.enterMode = CKEDITOR.ENTER_BR;
	config.toolbar = [
		{ name: 'text', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'] },
		{ name: 'style', items: ['TextColor'] },
		{ name: 'specialchar', items: ['SpecialChar'] },
		{ name: 'formatting', items: ['NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
		{ name: 'links', items: ['Link', 'Unlink'] },
		{ name: 'table', items: ['Table'] },
//		'/',
		{ name: 'editing', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'Undo', 'Redo'] },
		{ name: 'search', items: ['Find', 'Replace', 'SelectAll', 'Scayt'] },
	];
};
