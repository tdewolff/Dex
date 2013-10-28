<?php

Hooks::attach('main', 0, function() {
    global $db;
    Module::set('pages');

    $content = '';
    $link_data = Module::getLinkData();
    if ($page = $db->querySingle("SELECT * FROM module_pages WHERE link_module_id = '" . $db->escape($link_data['link_module_id']) . "' LIMIT 1;"))
        $content = $page['parsed_content'];

    Module::assign('content', $content);
    Module::render('index.tpl');
});

?>