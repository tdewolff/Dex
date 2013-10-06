$(function() {
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

    $('html').click(function() {
        $('.popbox .box').hide();
        $('.dropdown-menu').hide();
    });

    $('.popbox').click(function(e) {
        e.stopPropagation();
    });

    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    $('.dropdown-toggle').click(function(e) {
        e.stopPropagation();

        var dropdown = $(this).parent();
        $('.dropdown-menu', dropdown).toggle();
        if ($('.dropdown-menu', dropdown).is(':visible') && $('.popbox', dropdown).length > 0) {
                $('.popbox', dropdown).popbox();
        }
    });

    if ($('.popbox').length > 0) {
        $('.popbox').popbox();
    }

    if ($('.markdown').length > 0) {
        $('.markdown').markItUp(mySettings);
    }

    $('textarea.bottom').each(function () {
        $(this).scrollTop($(this)[0].scrollHeight);
    });

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
