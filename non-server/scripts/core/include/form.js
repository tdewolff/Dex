var Form = function(form) {
    var self = this;

    this.form = $(form);
    this.optionals = JSON.parse(this.form.attr('data-optionals'));
    this.needsSave = false;

    this.updateUnused = function(name) {
        $.each(self.optionals, function(i, optional) {
            if (typeof name == 'undefined' || $.inArray(name.slice(0, -9), optional) !== -1) {
                inputs = $();
                $.each(optional, function(i, name) {
                    inputs = inputs.add('[name="' + name + '"], [data-name="' + name + '"]');
                });

                var all_empty = true;
                inputs.each(function(i, input) {
                    if ($(input).val().length)
                    {
                        all_empty = false;
                        return false; // break
                    }
                });

                if (all_empty)
                    inputs.addClass('unused');
                else
                    inputs.removeClass('unused');
                return (typeof name == 'undefined'); // break on false
            }
        });
    };

    this.input = function(input) {
        var name = input.attr('name');
        if (typeof name == 'undefined')
            name = input.attr('data-name');

        if (typeof name != 'undefined')
            self.updateUnused(name);
        self.needsSave = true;
    };

    this.intervalSave = function() {
        if (self.needsSave == true) {
            self.needsSave = false;
            self.save();
        }
    };

    this.save = function() {
        // put data of multi-input fields into single hidden input
        self.form.find('input[type="hidden"]').each(function(i, hidden) {
            hidden = $(hidden);
            var name = hidden.attr('name');
            if (typeof name != 'undefined')
            {
                var data = [];
                self.form.find('[data-name="' + name + '"]').each(function(i, input) {
                    var value = $(input).val();
                    if (value.length)
                        data.push(value);
                });

                if (data.length)
                {
                    if (hidden.attr('data-type') == 'password' && data.length == 1)
                    {
                        if (data[0] != '********')
                            hidden.val(Sha1.hash(data[0]));
                    }
                    else
                        hidden.val(JSON.stringify(data));
                }
                else
                    hidden.val('');
            }
        });

        // AJAX
        api(window.location.href, self.form.serialize(), self.success, self.responseError);
    };

    this.success = function(data) {
        if (data['errors'].length) {
            var errors = data['errors'].join('<br>');
            var form_errors = self.form.find('.form_errors');
            if (form_errors.html() != errors)
                form_errors.html(errors).hide();
            form_errors.fadeIn();
        }
        else
            self.form.find('.form_errors').hide();

        if (data['item_errors'].length)
            $.each(data['item_errors'], function(i, item_error) {
                var input = self.form.find('[name="' + item_error['name'] + '"], [data-name="' + item_error['name'] + '"]');
                input.addClass('invalid');

                var error_box = self.form.find('.form_item_error[data-for-name="' + item_error['name'] + '"]');
                if (error_box.find('span').text() != item_error['error'])
                {
                    error_box.hide();
                    error_box.find('span').text(item_error['error']);
                }
                error_box.fadeIn();
            });
        else
            self.form.find('.form_item_error').hide();

        if (data['errors'].length || data['item_errors'].length)
            self.responseError();
        else if (data['redirect'].length > 0)
            window.location.replace(data['redirect']);
        else
            self.responseSuccess();
    };

    this.responseSuccess = function() {
        self.form.find('.form_response > .error').hide();
        self.form.find('.form_response > .success').fadeIn('fast');
    };

    this.responseError = function(data) {
        self.form.find('.form_response > .success').hide();
        self.form.find('.form_response > .error').fadeIn('fast');
    };

    // saving interval if no submit
    if (!this.form.find('button[type="submit"]').length)
        setInterval(self.intervalSave, 1000);

    this.updateUnused();
    this.form.on('input', 'input', function(e) {
        self.input($(e.currentTarget));
    });

    this.form.on('submit', function(e) {
        e.preventDefault();
        self.save();
    });

    // other stuff
    this.form.on('click', 'a.insert-link', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_link/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();

                    if (!text)
                        text = $('#insert_title').val();

                    textarea.insertAtCaret('[' + text + '](' + encodeURI(url) + ' "' + title + '")');
                }
            }
        });
    });

    this.form.on('click', 'a.insert-image', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_image/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();
                    var width = $('#insert_width').val();
                    var position = $('#insert_position').val();

                    if (!text)
                        text = $('#insert_title').val();
                    if (width)
                        url += '?w=' + width;

                    var insert = '![' + text + '](' + encodeURI(url) + ' "' + title + '")';
                    if (position)
                        insert = '<span style="float:' + position + ';">' + insert + '</span>';

                    textarea.insertAtCaret(insert);
                }
            }
        });
    });

    this.form.on('click', 'a.insert-asset', function() {
        var textarea = $('[name="' + $(this).attr('data-for-name') + '"]');
        $.fancybox.open({
            'type': 'ajax',
            'href': '/' + base_url + 'admin/auxiliary/insert_asset/',
            beforeShow: function() {
                $('.fancybox-skin').css('background', 'white')
            },
            beforeClose: function() {
                if ($('#insert_submit').val() == 1 && $('#insert_url').val())
                {
                    var title = $('#insert_title').val();
                    var url = $('#insert_url').val();
                    var text = $('#insert_text').val();

                    if (!text)
                        text = $('#insert_title').val();

                    textarea.insertAtCaret('[' + text + '](' + encodeURI(url) + ' "' + title + '")');
                }
            }
        });
    });
};

$('form').each(function(i, form) {
    new Form(form);
});

// form password
$('form input[data-type="password"]').each(function(i, password) {
    password = $(password);
    if (password.val().length)
        $('form input[data-name="' + password.attr('name') + '"]').val('********');
});

// form array
$('form input[data-type="array"]').each(function(i, array) {
    array = $(array);
    var template = doT.template($('#' + array.attr('data-template')).text()),
        ul = $('#' + array.attr('data-ul')),
        placeholders = []
        values = [];

    try {
        placeholders = JSON.parse(array.attr('placeholder'));
        values = JSON.parse(array.val());
    } catch (e) {}

    if (!values.length && placeholders.length)
        $.each(placeholders, function(i, placeholder) {
            ul.append(template({placeholder: placeholder, value: ''}));
        });
    else
    {
        values.push('');
        $.each(values, function(i, value) {
            ul.append(template({placeholder: '', value: value}));
        });
    }

    ul.on('input', 'input', function(e) {
        var input = $(this),
            li = input.closest('li');

        if (input.val().length > 0 && li.next().length == 0)
            $(template({value: ''})).appendTo(ul).hide().fadeIn();
    });
});

// form parameters
$('form input[data-type="parameters"]').each(function(i, array) {
    array = $(array);
    var template = doT.template($('#' + array.attr('data-template')).text()),
        ul = $('#' + array.attr('data-ul')),
        values = [];

    try {
        values = JSON.parse(array.val());
    } catch (e) {}
    values.push('');

    $.each(values, function(key, value) {
        ul.append(template({key: key, value: value}));
    });

    ul.on('input', 'input', function(e) {
        var input = $(this),
            li = input.closest('li');

        if (input.val().length > 0 && li.next().length == 0)
            $(template({key: '', value: ''})).appendTo(ul).hide().fadeIn();
    });
});