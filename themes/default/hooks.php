<?php

Hooks::preAttach('header', function () {
    Dexterous::addStyle('themes/default/resources/styles/style.css');
    Dexterous::addDeferredScript('themes/default/resources/scripts/tinynav.js');
});

?>
