<?php

Hooks::attach('navigation', 0, function($p) {
    global $db;
    $current_link = $p['link_id'];

    $menu = array();
    $table = $db->query("SELECT * FROM module_menu ORDER BY position ASC;");
    while ($row = $table->fetch())
        if ($link = $db->querySingle("SELECT * FROM links WHERE id = '" . $db->escape($row['link_id']) . "' LIMIT 1;"))
            $menu[$row['parent_id']][$row['id']] = array(
                'name' => $row['name'],
                'link' => $link['link'],
                'selected' => ($current_link == $link['id'] ? '1' : '0')
            );

    Dexterous::assign('menu', $menu);
    Dexterous::renderModule('menu', 'index.tpl');
});

?>