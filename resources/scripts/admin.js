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

    $('.page-wrapper, .page-wrapper-slim')
        .css({
            marginTop: '-10px',
            opacity: '0',
            display: 'block'
        })
        .animate({
            marginTop: '0',
            opacity: '1'
        }, 400, 'swing');
});

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
