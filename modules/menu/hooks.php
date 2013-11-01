<?php

Hooks::attach('navigation', 0, function() {
    global $db;
    Module::set('menu');

    $menu = array();
    $link_id = Module::getLinkId();
    $table = $db->query("SELECT *, link.link_id FROM module_menu
        JOIN link ON module_menu.link_id = link.link_id ORDER BY position ASC;");
    while ($row = $table->fetch())
    {
        $menu[$row['position']] = $row;
        $menu[$row['position']]['selected'] = ($link_id == $row['link_id'] ? '1' : '0');
    }
    ksort($menu);

    Module::assign('menu', $menu);
    Module::render('index.tpl');
});

?>