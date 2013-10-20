<?php

Hooks::attach('header', -1, function () {
    Theme::set('default');

    Core::addStyle('normalize.css');

    Theme::addStyle('style.css');
});

?>
