<?php

Hooks::attach('main', 0, function() {
    $link_id = Core::getLinkId();
    // TODO: future implementation of versioning
    $content = Db::querySingle("SELECT * FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'content' ORDER BY modify_time " . /*(User::loggedIn() ? "DESC" : "ASC")*/ "DESC" . " LIMIT 1;");
    if ($content)
        Template::assign('content', $content['content']);

    Template::render('index.tpl');
});

?>
