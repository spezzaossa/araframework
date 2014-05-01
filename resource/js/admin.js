if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
		"use strict";
		if (this == null) {
			throw new TypeError();
		}
		var t = Object(this);
		var len = t.length >>> 0;
		if (len === 0) {
			return -1;
		}
		var n = 0;
		if (arguments.length > 1) {
			n = Number(arguments[1]);
			if (n != n) { // shortcut for verifying if it's NaN
				n = 0;
			} else if (n != 0 && n != Infinity && n != -Infinity) {
				n = (n > 0 || -1) * Math.floor(Math.abs(n));
			}
		}
		if (n >= len) {
			return -1;
		}
		var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
		for (; k < len; k++) {
			if (k in t && t[k] === searchElement) {
				return k;
			}
		}
		return -1;
	}
}

if (!Array.prototype.filter)
{
	Array.prototype.filter = function(fun /*, thisp*/)
	{
		"use strict";

		if (this == null)
			throw new TypeError();

		var t = Object(this);
		var len = t.length >>> 0;
		if (typeof fun != "function")
			throw new TypeError();

		var res = [];
		var thisp = arguments[1];
		for (var i = 0; i < len; i++)
		{
			if (i in t)
			{
				var val = t[i]; // in case fun mutates this
				if (fun.call(thisp, val, i, t))
					res.push(val);
			}
		}

		return res;
	};
}

Array.prototype.diff = function(a) {
    return this.filter(function(i) {return (a.indexOf(i) < 0);});
};

Array.prototype.clone = function() {
	return this.slice(0);
};

jQuery.browser = {};
jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());

jQuery(document).ready(function(){
	if (jQuery(".date").length)	jQuery(".date").datepicker({dateFormat: "dd/mm/yy"});

	jQuery(window).resize(function() {
		if (oTable) oTable.fnAdjustColumnSizing(false);
	});

	jQuery('.collapse').on('show.bs.collapse', function(e) {
		var elem = jQuery(this).parent().find('.panel-title').find('.glyphicon-chevron-down');
		elem.removeClass('glyphicon-chevron-down');
		elem.addClass('glyphicon-chevron-up');
		e.stopPropagation();
	});

	jQuery('.collapse').on('hide.bs.collapse', function(e) {
		var elem = jQuery(this).parent().find('.panel-title').find('.glyphicon-chevron-up');
		elem.removeClass('glyphicon-chevron-up');
		elem.addClass('glyphicon-chevron-down');
		e.stopPropagation();
	});
});

function convertToSlug(text)
{
    return text
        .toLowerCase()
        .replace(/[\/]/g,'-')
        .replace(/[^\w- ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

function proposeSlug(text, id)
{
	var result = false;

	jQuery.ajax({
		async: false,
		type: 'POST',
		url: "admin-slugs-check",
		dataType: 'json',
		success: function(e) {
			result = e;
		},
		data: {
			'slug': text,
			'id': id
		}
	});

	return (result > 0);
}

function dataTablesBootstrapIntegration()
{
	jQuery('.dataTables_filter label').addClass('input-group');
	jQuery('.dataTables_filter label').prepend('<span class="input-group-addon">Cerca in tutte le colonne:</span>');
	jQuery('.dataTables_filter input').addClass('form-control');
	jQuery('.dataTables_length select').addClass('form-control');
}

function qqFileUploaderBootstrapIntegration()
{
	jQuery('.qq-upload-button').addClass('btn');
	jQuery('.qq-upload-button').addClass('btn-primary');
	jQuery('.qq-upload-drop-area').addClass('btn');
	jQuery('.qq-upload-drop-area').addClass('btn-default');
	jQuery('.qq-upload-list').addClass('col-md-12');
	jQuery('.qq-upload-drop-area').addClass('col-md-12');
	jQuery('.qq-upload-extra-drop-area').addClass('col-md-12');
}