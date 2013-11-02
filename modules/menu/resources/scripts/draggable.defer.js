$(function() {
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

var draggee = false,
    draggee_x_start = 0,
    draggee_y_offset = 0;

$('li .icon-eye-open').mousedown(function(e) {
    e.preventDefault();

    var ul = $(this).closest('ul');
    var li = $(this).closest('li');

    li.toggleClass('unused');
    li.find('input').toggleClass('unused');

    var level = li.attr('data-level');
    var elements = li.nextAll('li').filter(function() {
        return !$(this).hasClass('placeholder');
    }).each(function() {
        var element = $(this);
        if (element.attr('data-level') <= level)
            return false;

        if (element.hasClass('unused') != li.hasClass('unused'))
        {
            element.toggleClass('unused');
            element.find('input').toggleClass('unused');
        }
    });
    save(ul);
});

$('li .icon-reorder').mousedown(function(e) {
    e.preventDefault();

    if (draggee === false && e.which == 1) {
        draggee = $(this).closest('li');

        draggee_x_start = e.pageX - $('.icon-long-arrow-right:visible', draggee).length * 40.0;
        draggee_y_offset = e.pageY - (draggee.offset().top + 1);

        draggee.after('<li class="placeholder"></li>');
        draggee.css({
            'position': 'absolute',
            'top': (draggee.offset().top + 1) + 'px',
            'left': draggee.offset().left + 'px'
        }).addClass('draggee');

        restyleDraggableList(draggee.closest('ul'));
        restyleList(draggee.closest('ul'));

        $(document).bind('mousemove', drag);
    }
});

function drag(e) {
    if (draggee !== false) {
        var ul = draggee.closest('ul');
        var placeholder = $('.placeholder', ul);
        var elements = $('li:not(:first)', ul).filter(function() {
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

        // placement
        var places = elements.filter(function() {
             return !$(this).hasClass('placeholder');
        });

        if (places.length) {
            var y = top + 0.5 * places.first().outerHeight();
            places.each(function() {
                var place = $(this);
                if (y >= place.offset().top && y < place.offset().top + place.outerHeight()) {
                    if (top < placeholder.offset().top)
                        placeholder.insertBefore(place);
                    else
                        placeholder.insertAfter(place);
                    return false;
                }
            });
        }

        // X movement determines level of the item
        var level = 0;
        var previous = false;
        elements.each(function() {
            var element = $(this);
            if (element.hasClass('placeholder'))
                return false;
            previous = element;
        });

        if (previous !== false && typeof previous.attr('data-level') != 'undefined')
        {
            level = Math.floor((e.pageX - draggee_x_start + 20.0) / 40.0);
            var previous_level = previous.attr('data-level');
            if (level >= previous_level + 2)
                level = previous_level + 1;
        }

        $('.icon-long-arrow-right', draggee).hide();
        for (var i = 0; i < level; i++)
            $('.icon-long-arrow-right', draggee).eq(i).show();

        // apply CSS
        draggee.css('top', top + 'px');
        draggee.attr('data-level', level);

        restyleDraggableList(draggee.closest('ul'));
        restyleList(draggee.closest('ul'));
    }
    else
        $(document).unbind('mousemove', drag);
}

$('html').mouseup(function(e) {
    if (draggee !== false) {
        e.preventDefault();
        $(document).unbind('mousemove', drag);

        var ul = draggee.closest('ul');
        var placeholder = $('.placeholder', ul);

        var offset = draggee.offset().top - placeholder.offset().top;
        draggee.insertAfter(placeholder).removeClass('draggee');
        placeholder.remove();

        draggee.css({
            'position': 'relative',
            'top': offset + 'px',
            'left': '0'
        }).animate({
            'top': '0'
        }, 'fast', function() {
            $(this).css('position', 'static');
        });

        // toggle unused class
        if (draggee.prev('li').attr('data-level') < draggee.attr('data-level') && draggee.prev('li').hasClass('unused') && !draggee.hasClass('unused'))
        {
            draggee.toggleClass('unused');
            draggee.find('input').toggleClass('unused');
        }
        draggee = false;

        restyleDraggableList(ul);
        restyleList(ul);
        save(ul);
    }
});

function restyleDraggableList(ul) {
    $('li:not(:first), li:not(:first) > div', ul).css({
        '-webkit-border-radius': '0',
        '-moz-border-radius': '0',
        'border-radius': '0'
    });
}

function editingName(element) {
    var ul = $(element).closest('ul');
    save(ul);
}

var edittedUl = false;
function save(ul) {
    edittedUl = ul;
    $('.response > .loading, .response > .success, .reponse > .error').fadeOut('fast');
}

setInterval(function() {
    if (edittedUl !== false) {
        var ul = edittedUl;
        edittedUl = false;

        $('.response > .success, .reponse > .error').hide();
        $('.response > .loading').fadeIn('fast').css('display', 'inline-block');
        interval();

        var i = 0;
        var data = {};
        var elements = $('li:not(:first)', ul).filter(function() {
            return !$(this).hasClass('placeholder');
        }).each(function() {
            var element = $(this);
            data[i] = {
                link_id: element.attr('data-link-id'),
                level: element.attr('data-level'),
                name: element.find('input').val(),
                enabled: (element.hasClass('unused') ? '0' : '1')
            };
            i++;
        });

        ajax(false, 'POST', data, function() {
            $('.response > .loading').hide();
            $('.response > .success').fadeIn('fast');
            interval();
        }, function(element, data) {
            $('.response > .loading').hide();
            $('.response > .error').fadeIn('fast');

            var text = JSON.stringify(data);
            if (typeof data['responseText'] !== 'undefined')
                text = data['responseText'];
            else if (typeof data['statusText'] !== 'undefined')
                text = data['statusText'];

            $('#ajax_error, #ajax_error_link').remove();
            $('body').append('<a href="#ajax_error" id="ajax_error_link" class="hidden fancybox"></a>\
                <div id="ajax_error" class="hidden">' + text + '</div>');
            $('#ajax_error_link').fancybox().click();
        });
    }
}, 1000);