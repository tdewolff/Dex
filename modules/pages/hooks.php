<?php

Hooks::attach('module', function() {
    global $db, $link;

    $content = '';
    if ($link_module = $db->querySingle("SELECT id FROM link_modules WHERE link_id = '" . $db->escape($link['id']) . "' AND module_name = 'pages' LIMIT 1;"))
        if ($page = $db->querySingle("SELECT parsed_content FROM module_pages WHERE link_module_id = '" . $db->escape($link_module['id']) . "' LIMIT 1;"))
            $content = $page['parsed_content'];

    Dexterous::assign('content', $content);
    Dexterous::renderModule('pages', 'main', 'index.tpl');
});

?>