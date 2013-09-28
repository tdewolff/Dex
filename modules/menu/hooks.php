<?php

Hooks::attach('module', function() {
    global $db, $link;
    $current_link = $link['id'];

    $menu = array();
    $table = $db->query("SELECT * FROM `module_menu` ORDER BY position ASC;");
    while ($row = $table->fetch())
        if ($link = $db->querySingle("SELECT * FROM `links` WHERE id = '" . $db->escape($row['link_id']) . "' LIMIT 1;"))
        {
            $level = 0;
            $parent_id = $row['parent_id'];
            while ($parent_id != 0)
            {
                $parent_id = $menu[$parent_id]['parent_id'];
                $level++;
            }

            $menu[$row['id']] = array(
                'parent_id' => $row['parent_id'],
                'level' => $level,
                'selected' => ($current_link == $link['id'] ? '1' : '0'),
                'name' => $row['name'],
                'link' => $link['link']
            );
        }

    Dexterous::assign('menu', $menu);
    Dexterous::renderModule('menu', 'navigation', 'index.tpl');
});

?>