<?php

Hooks::attach('navigation', 0, function() {
    global $db;
    Module::set('menu');

    $menu = array();
    $table = $db->query("SELECT * FROM menu
        JOIN link ON menu.link_id = link.link_id ORDER BY position ASC;");
    while ($row = $table->fetch())
        $menu[$row['parent_id']][$row['link_id']] = array(
            'name' => $row['name'],
            'url' => $row['url'],
            'selected' => (Module::getLinkId() == $row['link_id'] ? '1' : '0')
        );

    Module::assign('menu', $menu);
    Module::render('menu.tpl');
});

?>