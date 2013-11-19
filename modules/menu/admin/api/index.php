<?php

if (API::action('modify_menu'))
{
    $db->exec("DELETE FROM module_menu;");

    foreach (API::get('menu') as $i => $item) {
        $db->exec("INSERT INTO module_menu (link_id, position, level, name, enabled) VALUES (
            '" . $db->escape($item['link_id']) . "',
            '" . $db->escape($i) . "',
            '" . $db->escape($item['level']) . "',
            '" . $db->escape($item['name']) . "',
            '" . $db->escape($item['enabled']) . "'
        );");
    }
    API::finish();
}

$menu = array();
$non_menu = array();
$table = $db->query("SELECT *, link.link_id AS link_id FROM link
    LEFT JOIN module_menu ON link.link_id = module_menu.link_id
    ORDER BY module_menu.position ASC;");
while ($row = $table->fetch())
    if (isset($row['module_menu_id']))
        $menu[] = $row;
    else
        $non_menu[] = $row;

foreach ($non_menu as $item) {
    $item['name'] = $item['title'];
    $item['level'] = '0';
    $item['enabled'] = '0';
    $menu[] = $item;
}

API::set('menu', $menu);
API::finish();

?>