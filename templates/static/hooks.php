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
    global $db, $base_url;

    $link_id = Core::getLinkId();
    $content = $db->querySingle("SELECT * FROM content WHERE link_id = '" . $db->escape($link_id) . "' LIMIT 1;");

    Template::assign('content', SmartyPants($content['content']));
    Template::render('index.tpl');
});

?>