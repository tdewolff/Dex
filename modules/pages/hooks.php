<?php

Hooks::attach('main', 0, function($p) {
    global $db;
    Module::set('contact');

    $current_link = $p['link_id'];

    $content = '';
    if ($page = $db->querySingle("SELECT * FROM link_modules
        JOIN module_pages ON link_modules.id = module_pages.link_module_id
        WHERE link_id = '" . $db->escape($current_link) . "' AND module_name = 'pages' LIMIT 1;"))
        $content = $page['parsed_content'];

    Module::assign('content', $content);
    Module::render('index.tpl');
});

?>