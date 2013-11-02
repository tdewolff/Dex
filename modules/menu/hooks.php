<?php

Hooks::attach('navigation', 0, function() {
    global $db;
    Module::set('menu');

    $menu = array();
    $link_id = Module::getLinkId();
    $table = $db->query("SELECT * FROM module_menu
        JOIN link ON module_menu.link_id = link.link_id
        WHERE enabled = '1' ORDER BY position ASC;");
    while ($row = $table->fetch())
    {
        $row['selected'] = ($link_id == $row['link_id'] ? '1' : '0');
        $menu[] = $row;
    }

    Module::assign('menu', $menu);
    Module::render('index.tpl');
});

?>