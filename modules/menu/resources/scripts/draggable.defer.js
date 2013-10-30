$(function() {
    $('li i').css({
        'display': 'inline-block',
        'width': '40px',
        'text-align': 'center'
    });

    $('.icon-long-arrow-right').hide();
    $('li').each(function() {
        var level = $(this).attr('data-level');
        for (var i = 0; i < level; i++)
            $('.icon-long-arrow-right', this).eq(i).show();
    });

    $('ul.draggable').each(function() {
        restyleDraggableList(this);
        restyleList(this);
    });
});

var draggee,
    draggee_x_start = 0,
    draggee_y_offset = 0;

$('li .icon-reorder').mousedown(function(e) {
    e.preventDefault();
    if (e.which == 1) {
        draggee = $(this).closest('li');

        draggee_x_start = e.pageX - $('.icon-long-arrow-right:visible', draggee).length * 40.0;
        draggee_y_offset = e.pageY - (draggee.offset().top + 1);

        draggee.after('<li class="placeholder ' + (draggee.hasClass('unused') ? 'unused' : '') + '"></li>');
        draggee.css({
            'position': 'absolute',
            'top': (draggee.offset().top + 1) + 'px',
            'left': draggee.offset().left + 'px',
            'border-top-width': '0',
            'height': '32px',
            'cursor': 'move'
        });

        restyleDraggableList(draggee.closest('ul'));
        restyleList(draggee.closest('ul'));

        $(document).bind('mousemove', drag);
    }
});

function drag(e) {
    var y = e.pageY;
    var ul = draggee.closest('ul');
    var placeholder = $('.placeholder', ul);

    if (e.which == 1) // drag
    {
        var elements = $('li:visible', ul).filter(function() {
            return $(this).css('position') == 'static';
        });

        // restrain Y movement within the list
        var top = e.pageY - draggee_y_offset;
        var min_top = elements.first().offset().top + 1;
        var max_top = elements.last().offset().top + 1;
        if (top < min_top)
            top = min_top;
        else if (top > max_top)
            top = max_top;

        draggee.css({
            'top': top + 'px'
        });

        // placement
        var places = elements.not(':first').filter(function() {
            return !$(this).hasClass('placeholder');
        });

        var place_halfheight = 0.5 * places.first().outerHeight();
        var border_halfheight = 0.5 * $('.border', ul).outerHeight();

        var last = false;
        var border_i = false;
        places.each(function(i) {
            var hover = false;
            if ($(this).hasClass('border'))
                hover = (y >= $(this).offset().top - place_halfheight && y < $(this).offset().top + border_halfheight);
            else if (i - 1 == border_i)
                hover = (y >= $(this).offset().top - border_halfheight && y < $(this).offset().top + place_halfheight);
            else
                hover = (y >= $(this).offset().top - place_halfheight && y < $(this).offset().top + place_halfheight);

            if (hover) {
                placeholder.insertBefore($(this));
                if (placeholder.hasClass('unused') != (border_i !== false))
                {
                    placeholder.toggleClass('unused');
                    draggee.toggleClass('unused');
                }
                last = false;
                return false;
            }

            last = $(this);
            if ($(this).hasClass('border'))
                border_i = i;
        });

        if (last !== false && y >= last.offset().top + 0.5 * last.outerHeight())
        {
            placeholder.insertAfter(last);
            if (placeholder.hasClass('unused') != true)
            {
                placeholder.toggleClass('unused');
                draggee.toggleClass('unused');
            }
        }

        // X movement determines level of the item
        var level = 0;
        var previous = $('.placeholder').prev('li');
        if (!draggee.hasClass('unused') && previous.length > 0 && typeof previous.attr('data-level') != 'undefined')
        {
            level = Math.floor((e.pageX - draggee_x_start + 20.0) / 40.0);
            var previous_level = previous.attr('data-level');
            if (level >= previous_level + 2)
                level = previous_level + 1;
        }

        $('.icon-long-arrow-right', draggee).hide();
        for (var i = 0; i < level; i++)
            $('.icon-long-arrow-right', draggee).eq(i).show();

        restyleDraggableList(draggee.closest('ul'));
        restyleList(draggee.closest('ul'));
    }
    else if (e.which == 0) // done
    {
        $(document).unbind('mousemove', drag);

        draggee.insertAfter(placeholder);
        placeholder.remove();

        draggee.css({
            'position': 'static',
            'border-top-width': '1px',
            'height': '33px',
            'cursor': 'auto'
        });

        restyleDraggableList(ul);
        restyleList(ul);
        save(ul);
    }
}

function restyleDraggableList(ul) {
    $('li:not(:first), li:not(:first) > div', ul).css({
        '-webkit-border-radius': '0',
        '-moz-border-radius': '0',
        'border-radius': '0'
    });

    var used = $('li:visible', ul).not(':first').filter(function() {
        return $(this).css('position') == 'static' && !$(this).hasClass('empty') && !$(this).hasClass('border') && !$(this).hasClass('unused');
    });
    var unused = $('li:visible', ul).not(':first').filter(function() {
        return $(this).css('position') == 'static' && !$(this).hasClass('empty') && !$(this).hasClass('border') && $(this).hasClass('unused');
    });

    if (used.length == 0)
        $('li.empty', ul).not('.unused').show();
    else
        $('li.empty', ul).not('.unused').hide();

    if (unused.length == 0)
        $('li.empty.unused', ul).show();
    else
        $('li.empty.unused', ul).hide();
}

var last_editing = 0;
function editingName(element) {
    clearTimeout(last_editing);
    last_editing = setTimeout(save, 1000);
}

function save(ul) {
    $('.response > .success, .reponse > .error').hide();
    $('.response > .loading').fadeIn('fast').css('display', 'inline-block');
    interval();

    var data = {};
    var used = $('li:visible', ul).not(':first').filter(function() {
        return $(this).css('position') == 'static' && !$(this).hasClass('empty') && !$(this).hasClass('border') && !$(this).hasClass('unused');
    }).each(function() {
        data[$(this).attr('data-link-id')] = {
            level: $(this).attr('data-level'),
            name: $(this).find('input').val()
        };
        $(this).find('span').text($(this).find('input').val());
    });

    ajax(false, 'PUT', data, function() {
        $('.response > .loading').hide();
        $('.response > .success').fadeIn();
        interval();
    }, function() {
        console.log(JSON.stringify(error));
        $('.response > .loading').hide();
        $('.response > .error').fadeIn();
    });
}