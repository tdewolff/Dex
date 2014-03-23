<?php

Hooks::attach('main', 0, function () {
	global $base_url;

    $link_id = Core::getLinkId();
    // TODO: future implementation of versioning
    $content = Db::singleQuery("SELECT * FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'content' ORDER BY modify_time " . /*(User::loggedIn() ? "DESC" : "ASC")*/ "DESC" . " LIMIT 1;");
    if ($content)
    {
    	$content['content'] = preg_replace('/\[base_url\]/', $base_url, $content['content']);
        Template::assign('content', $content['content']);
    }

    Template::render('index.tpl');
});

?>
