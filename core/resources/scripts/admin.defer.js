function restyleList(ul) {
    var items = $(ul).children('li:visible').filter(function() {
        return $(this).css('position') == 'static';
    });

    var odd = false;
    var len = items.length;
    items.each(function(i) {
        if (i != 0 && !$(this).hasClass('border')) {
            if (odd)
                $(this).addClass('odd');
            else
                $(this).removeClass('odd');
            odd = !odd;
        }

        if (i == len - 1) {
            $(this).css({
                '-webkit-border-bottom-left-radius': '5px',
                '-moz-border-bottom-left-radius': '5px',
                'border-bottom-left-radius': '5px',
                '-webkit-border-bottom-right-radius': '5px',
                '-moz-border-bottom-right-radius': '5px',
                'border-bottom-right-radius': '5px'
            });

            $('div:first-of-type', this).css({
                '-webkit-border-bottom-left-radius': '5px',
                '-moz-border-bottom-left-radius': '5px',
                'border-bottom-left-radius': '5px'
            });

            $('div:last-of-type', this).css({
                '-webkit-border-bottom-right-radius': '5px',
                '-moz-border-bottom-right-radius': '5px',
                'border-bottom-right-radius': '5px'
            });
        }
    });
}

function hideRow(id) {
    $('#' + id + ' .popbox .box, #' + id + ' .dropdown-menu').hide(150);
    $('#' + id).slideUp('fast', function() {
        restyleList($(this).closest('ul'));
    });
}

function interval() {
    $('span.passed_time').each(function () {
        if ($('span', this).text() == '')
            $('span', this).text('just now');

        if ($(this).is(':visible')) {
            if (typeof $(this).attr('data-time') == 'undefined')
                $(this).attr('data-time', new Date().getTime());

            var diff = Math.round((new Date().getTime() - $(this).attr('data-time')) / 1000),
                then = new Date($(this).attr('data-time')),
                value;

            if (diff < 2)
                value = 'just now';
            else if (diff < 30)
                value = 'seconds ago';
            else if (diff < 60)
                value = 'half a minute ago';
            else if (diff < 120)
                value = '1 minute ago';
            else if (diff < 600)
                value = Math.round(diff / 60) + ' minutes ago';
            else
                value = then.getHours() + ':' + then.getMinutes();

            if ($('span', this).text() !== value)
                $(this).fadeOut(function () {
                    $('span', this).text(value);
                }).fadeIn();
        } else if (typeof $(this).attr('data-time') != 'undefined') {
            $(this).removeAttr('data-time');
            $('span', this).text('just now');
        }
    });
}
setInterval(interval, 5000);
interval();

$(function() {
    if ($('.markdown').length > 0) {
        $('.markdown').markItUp(mySettings);
    }

    if ($.fn.fancybox) {
        $(".fancybox").fancybox({
            padding: 0,
            arrows: false,
            closeClick: true,
            closeBtn: false,

            openEffect : 'elastic',
            openSpeed  : 150,

            closeEffect : 'elastic',
            closeSpeed  : 150,
        });
    }

    $('ul.table').each(function() {
        restyleList(this);
    });

    $('textarea.bottom').each(function () {
        $(this).scrollTop(this.scrollHeight - $(this).height());
    });
});

// dropdown
$('html').click(function() {
    $('.dropdown-menu').fadeOut('fast');
});

$('.dropdown-menu').click(function(e) {
    e.stopPropagation();
});

$('.dropdown-toggle').click(function(e) {
    e.stopPropagation();

    var dropdown = $(this).parent();
    $('.dropdown-menu').not($('.dropdown-menu', dropdown)).hide(150);
    $('.dropdown-menu', dropdown).toggle();
    if ($('.dropdown-menu', dropdown).is(':visible')) {
        $('.dropdown-menu', dropdown).css({
            overflow: 'visible'
        });
    }
});

// halt
$('a.halt').click(function() {
    $(this).hide();
    $(this).parent().find('a.sure').fadeIn('fast').css('display', 'block');
});

$('a.sure').mouseleave(function() {
    $(this).fadeOut('fast', function() {
        $(this).parent().find('a.halt').fadeIn('fast');
    });
});

//popbox
function showPopbox(item) {
    var box = $(item).closest('.popbox').find('.box');
    if (box.css('display') == 'block') {
        box.fadeOut('fast');
    } else {
        $('.arrow', box).css({'left': box.outerWidth()/2 - 13});
        $('.arrow-border', box).css({'left': box.outerWidth()/2 - 13});
        box.css({
            'top': $(item).outerHeight() + 10,
            'left': $(item).outerWidth()/2 - box.outerWidth()/2
        });
        box.fadeIn();
    }
}

$('html').click(function() {
    $('.popbox .box').fadeOut('fast');
});

$('.popbox .open').click(function(e) {
    e.stopPropagation();
    showPopbox(this);
});

$('.popbox .close').click(function(e) {
    e.stopPropagation();
    var box = $(this).closest('.popbox').find('.box');
    box.fadeOut('fast');
});