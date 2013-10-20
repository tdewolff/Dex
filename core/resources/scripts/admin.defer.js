function ajaxAction(url, callback) {
    $.ajax({
        type: 'GET',
        url: url,
        success: callback
    });
}

function hideTableRow(id) {
    $('#' + id + ' .popbox .box, #' + id + ' .dropdown-menu').hide(150);
    $('#' + id).fadeOut(500);
}

function interval() {
    $("span.passed_time").each(function () {
        var date = new Date(),
            diff = Math.round(date.getTime() / 1000) - $(this).data("time"),
            value;

        if (diff < 2) {
            value = 'just now';
        } else if (diff < 30) {
            value = 'few seconds ago';
        } else if (diff < 60) {
            value = 'half a minute ago';
        } else if (diff < 120) {
            value = '1 minute ago';
        } else if (diff < 600) {
            value = Math.round(diff / 60) + ' minutes ago';
        } else {
            value = date.getHours() + ':' + date.getMinutes();
        }

        if ($('span', this).text() !== value) {
            $(this).fadeOut(function () {
                $('span', this).text(value);
            }).fadeIn();
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
});

$('textarea.bottom').each(function () {
    $(this).scrollTop($(this)[0].scrollHeight);
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

//popbox
$('html').click(function() {
    $('.popbox .box').fadeOut('fast');
});

$('.popbox .open').click(function(e) {
    e.stopPropagation();

    var box = $(this).parent().find('.box');
    if (box.css('display') == 'block') {
        box.fadeOut('fast');
    } else {
        $('.arrow', box).css({'left': box.width()/2 - 13});
        $('.arrow-border', box).css({'left': box.width()/2 - 13});
        box.css({'top': $(this).height() + 10, 'left': (($(this).width()/2) - box.width()/2 )});
        box.css({'display': 'block'});
    }
});

$('.popbox .close').click(function(e) {
    var box = $(this).closest('.box');
    box.fadeOut('fast');
});

// draggable
var dragee;
$('tr i.icon-reorder').mousedown(function(event) {
    console.log('drag');
    if (event.which == 1) {
        var tr = $(this).closest('tr');
        var width = tr.width();

        var blankTr = '<tr style="height:' + tr.height() + 'px;">';
        $('td', tr).each(function() {
            blankTr += '<td></td>';
        });
        blankTr += '</tr>';

        tr.wrap('<div><table width="100%"></table></div>');
        dragee = $(this).closest('div');

        dragee.after(blankTr);
        dragee.css('width', width + 'px');
        dragee.css('position', 'absolute');

        $(document).bind('mousemove', drag);
    }
});

function drag(event) {
    if (event.which == 0) {
        console.log('undrag');
        $(document).unbind('mousemove', drag);
        //dragee.html($('tr', dragee).parent().html());
        //$('tr', dragee).unwrap();
    } else if (event.which == 1) {
        dragee.css('top', (event.pageY - dragee.height() / 2) + 'px');
    }
}
