<?php

Hooks::attach('site-header', -1, function () {
    Core::addStyle('normalize.css');
    Theme::addStyle('style.css');
});

Hooks::attach('header', 0, function () {
    global $settings;

    if (isset($settings['title']))
        echo '<h1>' . $settings['title'] . '</h1>';
    if (isset($settings['subtitle']))
        echo '<h2>' . nl2br($settings['subtitle']) . '</h2>';
});

Hooks::attach('footer', 0, function () {
    global $settings;

    echo 'Seattle Worlds Fair';
});

?>
