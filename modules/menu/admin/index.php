<?php
Module::set('menu');

if (Common::isMethod('POST'))
{
    $data = Common::getMethodData();
    $db->exec("DELETE FROM module_menu;");

    $position = 0;
    foreach ($data as $link_id => $item) {
        $db->exec("INSERT INTO module_menu (link_id, position, level, name) VALUES (
            '" . $db->escape($link_id) . "',
            '" . $db->escape($position) . "',
            '" . $db->escape($item['level']) . "',
            '" . $db->escape($item['name']) . "'
        );");
        $position++;
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
        $menu[$row['position']] = $row;
    else
        $non_menu[] = $row;
ksort($menu);

Module::addStyle('style.css');
Module::addDeferredScript('draggable.defer.js');

Hooks::emit('admin_header');

Module::assign('menu', $menu);
Module::assign('non_menu', $non_menu);
Module::render('admin/menu.tpl');

Hooks::emit('admin_footer');
exit;

?>
