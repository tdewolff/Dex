function form_attr_tryOrEmpty(item, attr) {
    return $(item).attr(attr) ? $(item).attr(attr) : '';
}

function form_append_array(parent, name, i) {
    var item = $('input[data-name="' + name + '"][data-type="array_item"][data-i="' + (i - 1) + '"]', parent);

    $(item).after('<span class="comma">,</span>');
    $(parent).append('<input type="text"\
        value=""\
        maxlength="' + form_attr_tryOrEmpty(item, 'maxlength') + '"\
        pattern="' + form_attr_tryOrEmpty(item, 'pattern') + '"\
        placeholder="' + form_attr_tryOrEmpty(item, 'placeholder') + '"\
        class="' + form_attr_tryOrEmpty(item, 'class') + '"\
        oninput=\'' + form_attr_tryOrEmpty(item, 'oninput') + '\'\
        data-type="array_item"\
        data-name="' + name + '"\
        data-i="' + i + '" />');
}

function form_append_parameters(parent, name, i) {
    var key   = $('input[data-name="' + name + '"][data-type="parameter_key"][data-i="' + (i - 1) + '"]', parent);
    var value = $('input[data-name="' + name + '"][data-type="parameter_value"][data-i="' + (i - 1) + '"]', parent);

    $(parent).append('<input type="text"\
        value=""\
        maxlength="' + form_attr_tryOrEmpty(key, 'maxlength') + '"\
        pattern="' + form_attr_tryOrEmpty(key, 'pattern') + '"\
        class="' + form_attr_tryOrEmpty(key, 'class') + '"\
        oninput=\'' + form_attr_tryOrEmpty(key, 'oninput') + '\'\
        data-type="parameter_key"\
        data-name="' + name + '"\
        data-i="' + i + '" />\
    \
    <span class="equal">=</span>\
    \
    <input type="text"\
        value=""\
        maxlength="' + form_attr_tryOrEmpty(value, 'maxlength') + '"\
        pattern="' + form_attr_tryOrEmpty(value, 'pattern') + '"\
        class="' + form_attr_tryOrEmpty(value, 'class') + '"\
        oninput=\'' + form_attr_tryOrEmpty(value, 'oninput') + '\'\
        data-type="parameter_value"\
        data-name="' + name + '"\
        data-i="' + i + '" />'
    );
}

$('input[data-type="password"]').each(function() {
    var parent = $(this).parent(),
        name = $(this).attr('name'),
        data = $(this).val();

    if (data)
        $('input[data-name="' + name + '"]', parent).val(data);
});

$('input[data-type="array"]').each(function() {
    var parent = $(this).parent(),
        name = $(this).attr('name'),
        i = 0,
        data = null;

    try {
        data = JSON.parse($(this).val());
    } catch (e) {}

    if (data)
        $.each(data, function(key, value) {
            $('input[data-name="' + name + '"][data-i="' + i + '"]', parent).val(value);
            i += 1;
            form_append_array(parent, name, i);
        });
});

$('input[data-type="parameters"]').each(function() {
    var parent = $(this).parent(),
        name = $(this).attr('name'),
        i = 0,
        data = null;

    try {
        data = JSON.parse($(this).val());
    } catch (e) {}

    if (data)
        $.each(data, function(key, value) {
            $('input[data-name="' + name + '"][data-type="parameter_key"][data-i="' + i + '"]', parent).val(key);
            $('input[data-name="' + name + '"][data-type="parameter_value"][data-i="' + i + '"]', parent).val(value);
            i += 1;
            form_append_parameters(parent, name, i);
        });
});

function form_empty_together(empty_together) {    // toggle 'unused' style for elements in 'empty_together' when all are empty
    var all_empty = true;
    $.each(empty_together, function(i, name) {
        $('[type!="hidden"][name="' + name + '"], [data-name="' + name + '"]').each(function() {
            if ($(this).val().length > 0)
            {
                all_empty = false;
                return false; // break
            }
        });
        return all_empty; // break on false, continue on true
    });

    if (all_empty)
        $.each(empty_together, function(i, name) {
            $('[type!="hidden"][name="' + name + '"], [data-name="' + name + '"]').addClass('unused');
        });
    else
        $.each(empty_together, function(i, name) {
            $('[type!="hidden"][name="' + name + '"], [data-name="' + name + '"]').removeClass('unused');
        });
}

function form_input(item, empty_together) {
    $('.form_item_error[data-for-name="' + item + '"]').slideUp();
    form_empty_together(empty_together);

    var type = $(item).data('type');
    if (type == 'array_item')
        form_input_array(item);
    else if (type == 'parameter_key' || type == 'parameter_value')
        form_input_parameter(item);
}

function form_input_array(item) {
    var parent = $(item).parent(),
        name = $(item).data('name'),
        val = $(item).val(),
        i = $(item).data('i') + 1;

    if (val.length > 0)  {
        if ($('input[data-name="' + name + '"][data-i="' + i + '"]', parent).length == 0) {
            // create new element
            form_append_array(parent, name, i);
            $('input[data-name="' + name + '"][data-i="' + i + '"]', parent).hide().show('normal');
        }/* else if (val.length > 0 && $('input[data-name="' + name + '"][data-i="' + i + '"]', parent).css("display") == 'none') {
            // show previously hidden element
            $('input[data-name="' + name + '"][data-i="' + (i - 1) + '"] + span, input[data-name="' + name + '"][data-i="' + i + '"]', parent).show('normal');
        }*/
    }/* else {
        // hide elements
        while ($('input[data-name="' + name + '"][data-i="' + (i - 1) + '"]', parent).length
            && $('input[data-name="' + name + '"][data-i="' + (i - 1) + '"]', parent).val().length == 0) {
            $('input[data-name="' + name + '"][data-i="' + (i - 1) + '"] + span, input[data-name="' + name + '"][data-i="' + i + '"]', parent).hide('normal');
            i--;
        }
    }*/
}

function form_input_parameter(item) {
    var parent = $(item).parent(),
        name = $(item).attr('name'),
        i = $(item).data('i') + 1;

    if ($('input[data-name="' + name + '"][data-i="' + i + '"]', parent).length === 0) {
        form_append_parameters(parent, name, i);
        $('input[data-name="' + name + '"][data-type="parameter_key"][data-i="' + i + '"],\
           input[data-name="' + name + '"][data-type="parameter_value"][data-i="' + i + '"]', parent).hide().show('normal');
    }
}

function form_submit(form, submitted) {
    if ($('input[name="' + submitted + '"]', form).val() === '1')
        return false;
    $('input[name="' + submitted + '"]', form).val('1');

    $('input[data-type="password"]', form).each(function() {
        var parent = $(this).parent(),
            name = $(this).attr('name'),
            data = $('input[data-name="' + name + '"]', parent).val();

        $(this).val('');
        if (data.length > 0)
            $(this).val(Sha1.hash(data));
        $(this).attr('disabled', 'disabled');
    });

    $('input[data-type="array"]', form).each(function() {
        var parent = $(this).parent(),
            name = $(this).attr('name'),
            items = $('input[data-name="' + name + '"]', parent),
            data = [];

        for (var i = 0; i < items.length; i++)
            if ($(items[i]).val().length)
                data.push($(items[i]).val());

        $(this).val('');
        if (data.length > 0)
            $(this).val(JSON.stringify(data));
    });

    $('input[data-type="parameters"]', form).each(function() {
        var parent = $(this).parent(),
            name = $(this).attr('name'),
            keys = $('input[data-name="' + name + '"][data-type="parameter_key"]', parent),
            vals = $('input[data-name="' + name + '"][data-type="parameter_val"]', parent),
            data = {};

        for (var i = 0; i < keys.length; i++)
            if ($(keys[i]).val().length)
                data[$(keys[i]).val()] = $(vals[i]).val();

        $(this).val('');
        if (data.length > 0)
            $(this).val(JSON.stringify(data));
    });

    console.log($(form).data('ajax'));
    if ($(form).data('ajax') === false) {
        return true;
    } else {
        console.log('ajax');
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: $(form).serialize(),
            success: function() {
                console.log('ajax success');
            },
            dataType: 'json'
        });
        return false;
    }
}

$('input, select').keydown(function(e) {
    if (e.keyCode === 13) {
        $(this).parents('form').submit();
    }
});

$('a.submit').click(function() {
    $(this).parents('form').submit();
});