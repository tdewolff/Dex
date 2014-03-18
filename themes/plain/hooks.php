<?php

Hooks::attach('site-header', -1, function () {
    Theme::addStyle('style.css');
    Theme::addStyle('yui-pure.min.css');
    Theme::addStyle('lora.font.css');
    Theme::addDeferredScript('jquery.min.js');
    Theme::addDeferredScript('script.js');
});

Hooks::attach('navigation', -1, function () {
    global $settings;
    echo '<a href="/' . Common::$base_url . '" class="logo">' . $settings['title'] . '</a>';
});

Hooks::attach('header', 0, function () {
    global $settings;

    if (isset($settings['title']))
        echo '<h1>' . $settings['title'] . '</h1>';
    if (isset($settings['subtitle']))
        echo '<h2>' . nl2br($settings['subtitle']) . '</h2>';
});

/* First run setup */
if (!file_exists('assets/fonts/lora')) {
    if (!file_exists('assets/fonts')) {
        mkdir('assets/fonts');
    }
    mkdir('assets/fonts/lora');
    copy('themes/plain/resources/fonts/lora/lora.woff', 'assets/fonts/lora/lora.woff');
    copy('themes/plain/resources/fonts/lora/lora-bold.woff', 'assets/fonts/lora/lora-bold.woff');
    copy('themes/plain/resources/fonts/lora/lora-italic.woff', 'assets/fonts/lora/lora-italic.woff');
    copy('themes/plain/resources/fonts/lora/lora-bold-italic.woff', 'assets/fonts/lora/lora-bold-italic.woff');
}
