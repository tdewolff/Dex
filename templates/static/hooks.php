<?php

use \Michelf\Markdown;
require_once('vendor/smartypants.php');

Hooks::attach('site-header', -1, function () {
    if (User::loggedIn())
    {
        Core::addStyle('vendor/grande-editor.css');
        Core::addStyle('vendor/grande-menu.css');

        Core::addDeferredScript('vendor/grande.js');
    }
});

Hooks::attach('main', 0, function() {
    $link_id = Core::getLinkId();
    $content = Db::querySingle("SELECT * FROM content WHERE link_id = '" . Db::escape($link_id) . "' LIMIT 1;");

    Template::assign('content', SmartyPants($content['content']));
    Template::render('index.tpl');
});

?>