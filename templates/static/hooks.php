<?php

Hooks::attach('site-header', -1, function () {
    if (User::loggedIn())
    {
        Core::addStyle('vendor/grande-editor.css');
        Core::addStyle('vendor/grande-menu.css');

        Core::addScript('vendor/grande.js');
    }
});

Hooks::attach('main', 0, function() {
    $link_id = Core::getLinkId();
    $content = Db::querySingle("SELECT * FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'content' ORDER BY modify_time " . (User::loggedIn() ? "DESC" : "ASC") . " LIMIT 1;");
    if ($content)
        Template::assign('content', $content['content']);

    Template::render('index.tpl');
});

?>
