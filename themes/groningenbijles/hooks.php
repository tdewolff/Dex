<?php

Hooks::preAttach('header', function () {
    Dexterous::addStyle('themes/groningenbijles/resources/styles/style.css');
    Dexterous::addDeferredScript('themes/groningenbijles/resources/scripts/tinynav.js');
});

?>
