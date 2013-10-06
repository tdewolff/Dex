<?php

Hooks::attach('header', -1, function () {
    Dexterous::addStyle('resources/styles/normalize.css');
    Dexterous::addStyle('themes/groningenbijles/resources/styles/style.css');
    Dexterous::addDeferredScript('themes/groningenbijles/resources/scripts/tinynav.js');
});

?>
