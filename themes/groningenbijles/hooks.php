<?php

Hooks::attach('header', -1, function () {
    Theme::set('groningenbijles');

    Core::addStyle('normalize.css');
    Core::addDeferredScript('jquery.js');

    Theme::addStyle('style.css');
    Theme::addDeferredScript('tinynav.js');
    Theme::addDeferredScript('script.defer.js');
});

?>
