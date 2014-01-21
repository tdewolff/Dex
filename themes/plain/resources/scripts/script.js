(function ($) {
    console.log($('#admin-bar'));
    if ($('#admin-bar').length !== 0) {
        $('nav').css('top', '33px');
        $('header').css('margin-top', '33px');
    }
}(jQuery));
