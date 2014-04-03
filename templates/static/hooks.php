<?php

Hooks::attach('main', 0, function () {
    $link_id = Core::getLinkId();
    // TODO: future implementation of versioning
    $content = Db::singleQuery("SELECT * FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'content' ORDER BY modify_time " . /*(User::loggedIn() ? "DESC" : "ASC")*/ "DESC" . " LIMIT 1;");
    if ($content)
    {
    	$content['content'] = preg_replace('/([src|href]=")\[base_url\]/', '\1/' . Common::$base_url, $content['content']);
        Template::set('content', $content['content']);

        $user = Db::singleQuery("SELECT * FROM user WHERE user_id = '" . Db::escape($content['user_id']) . "' LIMIT 1;");
        if ($user)
        {
            Template::set('author', $user['username']);
            Template::set('last_save', $content['modify_time']);
        }
    }
    else
        Template::set('content', 'Edit this&#x2026;');
    Template::render('index.tpl');
});

?>
