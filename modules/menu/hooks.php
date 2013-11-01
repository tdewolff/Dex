<?php

Hooks::attach('navigation', 0, function() {
    global $db;
    Module::set('menu');

    echo 'menu';

    $menu = array();
    $table = $db->query("SELECT *, link.link_id FROM module_menu
        JOIN link ON module_menu.link_id = link.link_id ORDER BY position ASC;");
    while ($row = $table->fetch())
    {
        $menu[$row['position']] = $row;
        $menu[$row['position']]['selected'] = (Module::getLinkId() == $row['link_id'] ? '1' : '0');
    }
    ksort($menu);

    Module::assign('menu', $menu);
    Module::render('menu.tpl');
});

?>