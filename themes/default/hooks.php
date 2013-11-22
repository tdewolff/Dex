<?php

Hooks::attach('site-header', -1, function () {
    Theme::set('default');
});

Hooks::attach('header', 0, function() {
    global $settings;

    if (isset($settings['title']))
        echo '<h1>' . $settings['title'] . '</h1>';
    if (isset($settings['subtitle']))
        echo '<h2>' . nl2br($settings['subtitle']) . '</h2>';
});

?>
