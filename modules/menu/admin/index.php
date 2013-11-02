<?php
Module::set('menu');

if (Common::isMethod('POST'))
{
    $data = Common::getMethodData();
    $db->exec("DELETE FROM module_menu;");

    foreach ($data as $i => $item) {
        $db->exec("INSERT INTO module_menu (link_id, position, level, name, enabled) VALUES (
            '" . $db->escape($item['link_id']) . "',
            '" . $db->escape($i) . "',
            '" . $db->escape($item['level']) . "',
            '" . $db->escape($item['name']) . "',
            '" . $db->escape($item['enabled']) . "'
        );");
    }
    exit;
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

Module::addStyle('draggable.css');
Module::addDeferredScript('draggable.defer.js');

Hooks::emit('admin_header');

Module::assign('menu', $menu);
Module::assign('non_menu', $non_menu);
Module::render('admin/menu.tpl');

Hooks::emit('admin_footer');
exit;

?>
