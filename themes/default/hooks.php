<?php

Hooks::attach('header', -1, function () {
    Dexterous::addStyle('themes/default/resources/styles/style.css');
    Dexterous::addDeferredScript('themes/default/resources/scripts/tinynav.js');
});

?>
