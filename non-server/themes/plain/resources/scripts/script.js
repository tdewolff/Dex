(function ($) {
    if ($('#admin-bar').length !== 0) {
        $('nav').css('top', '33px');
        $('header').css('margin-top', '33px');
    }
    $('#log-out').click(function () {
        $('nav').animate({
            'top': '0px'
        });
        $('header').animate({
            'margin-top': '0px'
        });
    });
}(jQuery));
