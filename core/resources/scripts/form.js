var Form = function (form) {
	var self = this;

	this.form = $(form);
	this.optionals = JSON.parse(this.form.attr('data-optionals'));

	if (!this.form.find('button[type="submit"]').length) {
		new Save(this.form);
	}

	this.updateUnused = function (name) {
		$.each(self.optionals, function(i, optional) {
			if (typeof name === 'undefined' || $.inArray(name, optional) !== -1) {
				inputs = $();
				$.each(optional, function(i, name) {
					inputs = inputs.add('[name="' + name + '"], [data-name="' + name + '"]');
				});

				var all_empty = true;
				inputs.each(function(i, input) {
					if ($(input).val().length) {
						all_empty = false;
						return false; // break
					}
				});

				if (all_empty) {
					inputs.addClass('unused');
				} else {
					inputs.removeClass('unused');
				}
				return (typeof name === 'undefined'); // break when it's the initial updateUnused call
			}
		});
	};
	this.updateUnused();

	this.save = function () {
		apiStatusWorking();

		// put data of multi-input fields into single hidden input
		self.form.find('input[type="hidden"]').each(function (i, hidden) {
			hidden = $(hidden);
			var name = hidden.attr('name');
			if (typeof name !== 'undefined' && name != 'nonce') {
				var data = [];
				self.form.find('[data-name="' + name + '"]').each(function (i, input) {
					var value = $(input).val();
					if (value.length)
						data.push(value);
				});

				if (data.length) {
					if (hidden.attr('data-type') == 'password' && data.length == 1) {
						if (data[0] != '********') { // hidden already has the original password value, so don't overwrite
							// TODO: uncomment
							//if (data[0].length < 8)
							//    hidden.val('tooshort');
							//else if (!/[a-z]/.test(data[0]) || !/[A-Z]/.test(data[0]) || !/[0-9]/.test(data[0]))
							//    hidden.val('incomplex');
							//else
								hidden.val(Sha1.hash(data[0]));
						}
					} else {
						hidden.val(JSON.stringify(data));
					}
				} else {
					hidden.val('');
				}
			}
		});
		api(window.location.href, self.form.serialize(), self.success, self.error); // AJAX
	};

	this.success = function (data) {
		self.form.find('.errors').hide();
		if (data['errors'].length) {
			var errors = data['errors'].join('<br>');
			var form_errors = self.form.find('.errors');
			if (form_errors.html() != errors)
				form_errors.html(errors).hide();
			form_errors.fadeIn();
		}

		self.form.find('.inline-error-below').hide();
		if (data['item_errors'].length) {
			$.each(data['item_errors'], function (i, item_error) {
				var input = self.form.find('[name="' + item_error['name'] + '"], [data-name="' + item_error['name'] + '"]');
				input.addClass('invalid');

				var error_box = self.form.find('.inline-error-below[data-for-name="' + item_error['name'] + '"]');
				if (error_box.find('span').text() != item_error['error']) {
					error_box.hide();
					error_box.find('span').text(item_error['error']);
				}
				error_box.fadeIn();
			});
		}

		if (data['errors'].length || data['item_errors'].length) {
			apiStatusError(data['response']['error']);
		} else if (data['redirect'].length > 0) {
			window.location.href = data['redirect'];
		} else {
			apiStatusSuccess(data['response']['success']);
		}
	};

	this.error = function (data) {
		apiStatusError();
	};

	this.form.on('input', 'input, textarea', function (e) {
		apiStatusClear();

		var input = $(e.currentTarget),
			name = input.attr('name');

		if (typeof name === 'undefined') {
			name = input.attr('data-name');
		} else {
			self.updateUnused(name);
		}
	});

	this.form.on('submit', function (e) {
		e.preventDefault();
		if (self.form.find('button[type="submit"]').length) { // make sure you can't double click the submit button
			self.form.find('button[type="submit"]').blur().attr('disabled', 'disabled');
			setTimeout(function () {
				self.form.find('button[type="submit"]').removeAttr('disabled');
			}, 1000);
		}
		self.save();
	});

	this.form.on('save', function () {
		self.save();
	});
};

$('form').each(function (i, form) {
	new Form(form);
});

// form password
$('form input[data-type="password"]').each(function (i, password) {
	password = $(password);
	if (password.val().length) {
		$('form input[data-name="' + password.attr('name') + '"]').val('********');
	}
});

// form array
$('form input[data-type="array"]').each(function (i, array) {
	array = $(array);
	var template = doT.template($('#' + array.attr('data-template')).text()),
		ul = $('#' + array.attr('data-ul')),
		placeholders = []
		values = [];

	try {
		placeholders = JSON.parse(array.attr('data-placeholder'));
		values = JSON.parse(array.val());
	} catch (e) {}

	if (!values.length && placeholders.length) {
		var items = '';
		$.each(placeholders, function (i, placeholder) {
			items += template({placeholder: placeholder, value: ''});
		});
		ul.append(items);
	} else {
		values.push('');
		var items = '';
		$.each(values, function (i, value) {
			items += template({placeholder: '', value: value});
		});
		ul.append(items);
	}

	ul.on('keydown', 'input', function (e) {
		if (e.keyCode === 188) {
			var input = $(this),
				li = input.closest('li');
			li.next().find('input').focus();
			e.preventDefault();
		}
	});

	ul.on('input', 'input', function (e) {
		var input = $(this),
			li = input.closest('li'),
			ul = li.closest('ul');

		if (li.next().length == 0 && input.val().length > 0) {
			$(template({placeholder: '', value: ''})).appendTo(ul).hide().fadeIn();
			ul.tooltip('close').tooltip('open');
		} else if (li.next().length > 0 && li.next().next().length == 0 && input.val().length == 0) { // remove empties
			var prev = li;
			li = li.next();
			while (prev.length && prev.find('input').val().length == 0) {
				li.remove();
				li = prev;
				prev = prev.prev();
			}
			ul.tooltip('close').tooltip('open');
		}
	});
});
