$(function() {
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
});

