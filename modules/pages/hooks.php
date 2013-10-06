<?php

Hooks::attach('main', 0, function($p) {
    global $db;
    $current_link = $p['link_id'];

    $content = '';
    if ($link_module = $db->querySingle("SELECT id FROM link_modules WHERE link_id = '" . $db->escape($current_link) . "' AND module_name = 'pages' LIMIT 1;"))
        if ($page = $db->querySingle("SELECT parsed_content FROM module_pages WHERE link_module_id = '" . $db->escape($link_module['id']) . "' LIMIT 1;"))
            $content = $page['parsed_content'];

    Dexterous::assign('content', $content);
    Dexterous::renderModule('pages', 'index.tpl');
});

?>