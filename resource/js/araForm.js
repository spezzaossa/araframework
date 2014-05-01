
function validateEmail(email) {
    //var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var re = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^\+?[0-9\- ]{5,}$/;
    return re.test(phone);
}

function validateNumber(o) {
	return ! isNaN (o-0) && o !== null && o.replace(/^\s\s*/, '') !== "" && o !== false;
}

function checkField(elem) {
	elem = jQuery(elem);
	var status = true;
	var type = elem.attr('type');
	var name = elem.attr('name');

	if (type === 'checkbox') {
		var group = null;
		if (elem.data('fieldgroup')) group = jQuery(elem.data('fieldgroup'));
		if (group) {
			if (group.hasClass('required')) {
				if (group.data('amount') && jQuery('[name=\''+name+'\']:checked').length != group.data('amount')) status = false;
				if (group.data('min-amount') && jQuery('[name=\''+name+'\']:checked').length < group.data('min-amount')) status = false;
				if (group.data('max-amount') && jQuery('[name=\''+name+'\']:checked').length > group.data('max-amount')) status = false;
			}
			(status) ? group.find('input').removeClass('invalid') : group.find('input').addClass('invalid');
			return status;
		} else {
			if (elem.hasClass('required') && !elem.is(':checked')) status = false;
		}
	}
	else if (type === 'radio') {
		var group = null;
		if (elem.data('fieldgroup')) group = jQuery(elem.data('fieldgroup'));
		if (group) {
			if (group.hasClass('required') && !jQuery('[name=\''+name+'\']:checked').length) status = false;

			(status) ? group.find('input').removeClass('invalid') : group.find('input').addClass('invalid');
			return status;
		} else {
			if (elem.hasClass('required') && !elem.is(':checked')) status = false;
		}
	}
	else {
		if (elem.hasClass('required') && !elem.val()) status = false;
		if (elem.hasClass('email') && !validateEmail(elem.val())) status = false;
		if (elem.hasClass('phone') && !validatePhone(elem.val())) status = false;
		if (elem.hasClass('number') && !validateNumber(elem.val())) status = false;
		if (elem.hasClass('date') && !moment(elem.val(), elem.data('format-momentjs'), true).isValid()) status = false;
	}

	(status) ? elem.removeClass('invalid') : elem.addClass('invalid');
	return status;
}

function checkForm(form) {
	var status = true;

	jQuery(form).find('.form-field').each(function(i,v) {
		status = status & checkField(this);
	});

	return status;
}

jQuery(window).ready(function() {
	jQuery('.araForm').submit(function(e){
		e.preventDefault();
		if (checkForm(this)) this.submit();
	});

	jQuery('.form-field').bind("change keyup", function() {
		checkField(this);
	});
	
	jQuery('.form-field.date').each(function(i,v) {
		var format = jQuery(v).data('format-datepicker');
		jQuery(v).datepicker({
			dateFormat: format
		});
	});
});
