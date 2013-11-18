<?php

Hooks::attach('header', -1, function () {
    Theme::set('default');

    Core::addStyle('include/normalize.css');

    Theme::addStyle('style.css');
});

?>
