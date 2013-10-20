function form_empty_together(empty_together) {
    // toggle 'unused' style for elements in 'empty_together' when all are empty
    var all_empty = true;
    jQuery.each(empty_together, function () {
        if ($('input[name="' + this + '"]').length != 0 && $('input[name="' + this + '"]').val().length > 0) {
            all_empty = false;
            return;
        }
    });

    if (all_empty) {
        jQuery.each(empty_together, function () {
            $('input[name="' + this + '"]').addClass('unused');
        });
    } else {
        jQuery.each(empty_together, function () {
            $('input[name="' + this + '"]').removeClass('unused');
        });
    }
}

function form_input(item, empty_together) {
    $(item).parent().next('.form_item_error').slideUp();
    form_empty_together(empty_together);
}

// TODO: make more general
function form_parameter(item) {
    var i = $(item).data('i') + 1,
        parent = $(item).parent(),
        name = $(item).attr('name');

    if ($('input[data-i="' + i + '"]', parent).length === 0) {
        var appendee = $('<input type="text" data-name="' + name + '" data-type="parameter_key" data-i="' + i + '" value="" oninput="form_parameter(this);" />' +
                         '<span class="parameter_equal">=</span><input type="text" data-name="' + name + '" data-type="parameter_val" data-i="' + i + '" value="" oninput="form_parameter(this);" />'
        ).hide();
        $(parent).append(appendee);
        appendee.show('normal');
    }
}

function form_submit(form, submitted) {
    if ($('input[name="' + submitted + '"]', form).val() === '1') {
        return false;
    }
    $('input[name="' + submitted + '"]', form).val('1');

    $('input[type="password"]', form).each(function () {
        if ($(this).val().length > 0) {
            $('input[name="' + $(this).attr('name') + '_hash"]').val(
                Sha1.hash($(this).val())
            );
        }
        $(this).attr('disabled', 'disabled');
    });

    $('input[data-type="parameters"]', form).each(function () {
        var i,
            data = {},
            parent = $(this).parent(),
            keys = $('input[data-type="parameter_key"]', parent),
            vals = $('input[data-type="parameter_val"]', parent);

        for (i = 0; i < keys.length; i++) {
            if ($(keys[i]).val().length) {
                data[$(keys[i]).val()] = $(vals[i]).val();
            }
        }
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

$('input, select').keydown(function (e) {
    if (e.keyCode === 13) {
        $(this).parents('form').submit();
    }
});

$('a.submit').click(function () {
    $(this).parents('form').submit();
});

$('input[data-type="parameters"]').each(function () {
    var data = null,
        i = 0,
        parent = $(this).parent(),
        name = $(this).attr('name'),
        key;

    try {
        data = JSON.parse($(this).val());
    } catch (e) {
    }

    for (key in data) {
        if (data.hasOwnProperty(key)) {
            $('input[data-type="parameter_key"][data-i="' + i + '"]', parent).val(key);
            $('input[data-type="parameter_val"][data-i="' + i + '"]', parent).val(data[key]);
            i += 1;

            var appendee = $('<input type="text" data-name="' + name + '" data-type="parameter_key" data-i="' + i + '" value="" oninput="form_parameter(this);" />' +
                             '<span class="parameter_equal">=</span><input type="text" data-name="' + name + '" data-type="parameter_val" data-i="' + i + '" value="" oninput="form_parameter(this);" />'
            );
            $(parent).append(appendee);
        }
    }
});