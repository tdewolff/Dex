<?php

Hooks::attach('site-header', -1, function () {
    Theme::set('pure');

    Theme::addExternalStyle('http://yui.yahooapis.com/pure/0.3.0/pure-min.css');
    Theme::addStyle('side-menu.css');
    Theme::addStyle('style.css');
    Theme::addExternalScript('//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js');
    Theme::addDeferredScript('script.js');
});

Hooks::attach('site-header', 1, function () {
    echo '<a href="#" id="menuLink" class="pure-menu-link"><span></span></a>';
});

Hooks::attach('header', 0, function() {
    global $link;
    if (isset($link['title']))
        echo '<h1>' . $link['title'] . '</h1>';
});

Hooks::attach('navigation', -1, function () {
    global $base_url, $settings;
    echo '<a href="/' . $base_url . '" class="pure-menu-heading">' . $settings['title'] . '</a>';
});

?>