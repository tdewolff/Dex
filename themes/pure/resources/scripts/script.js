$(function() {
    $('.page-wrapper').attr('id', 'layout');
    $('.navigation').attr('id', 'menu');
    $('header').addClass('header');
    $('.main').addClass('content');

    $('.navigation').addClass('pure-menu').addClass('pure-menu-open');
    $('.navigation .selected').addClass('pure-menu-selected');

    var layout   = document.getElementById('layout'),
        menu     = document.getElementById('menu'),
        menuLink = document.getElementById('menuLink');

    function toggleClass(element, className) {
        var classes = element.className.split(/\s+/),
            length = classes.length,
            i = 0;

        for(; i < length; i++) {
          if (classes[i] === className) {
            classes.splice(i, 1);
            break;
          }
        }
        // The className is not found
        if (length === classes.length) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    }

    menuLink.onclick = function (e) {
        var active = 'active';

        e.preventDefault();
        toggleClass(layout, active);
        toggleClass(menu, active);
        toggleClass(menuLink, active);
    };

    if ($('#admin-bar').length)
        $('#menu').css('top', '+=33px');

    $('#log-out').click(function() {
        $('#menu').animate({
            'top', '-=33px'
        });
});