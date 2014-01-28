<?php

if (!User::loggedIn())
    user_error('Forbidden access', ERROR);

/**
 * Clean up extra menu items:
 **/
    $doDelete = false;
    $delete_menu_items_query = Db::query('
        SELECT link_id FROM module_menu
        WHERE link_id NOT IN (
            SELECT link_id FROM link
        )');
    $delete_query = "DELETE FROM module_menu WHERE";
    while ($link_id = $delete_menu_items_query->fetch('link_id'))
    {
        $doDelete = true;
        $delete_query .= ' link_id = ' . $link_id . ' OR';
    }
    $delete_query = substr($delete_query, 0, strlen($delete_query) - 3);
    if ($doDelete)
    {
        Db::exec($delete_query);
    }
/**
 * Done cleaning up
 **/


if (API::action('modify_menu'))
{
    Db::exec("DELETE FROM module_menu;");

    foreach (API::get('menu') as $i => $item) {
        Db::exec("INSERT INTO module_menu (link_id, position, level, name, enabled) VALUES (
            '" . Db::escape($item['link_id']) . "',
            '" . Db::escape($i) . "',
            '" . Db::escape($item['level']) . "',
            '" . Db::escape($item['name']) . "',
            '" . Db::escape($item['enabled']) . "'
        );");
    }
    API::finish();
}
else if (API::action('get_menu'))
{
    $menu = array();
    $non_menu = array();
    $table = Db::query("SELECT *, link.link_id AS link_id FROM link
        LEFT JOIN module_menu ON link.link_id = module_menu.link_id
        ORDER BY module_menu.position ASC;");
    while ($row = $table->fetch())
    {
        if (!isset($row['module_menu_id']))
        {
            $row['level'] = 0;
            $row['name'] = $row['title'];
            $row['enabled'] = 0;
            $non_menu[] = $row;
        }
        else
            $menu[] = $row;
    }
    $menu = array_merge($menu, $non_menu); // non_menu items come last

    API::set('menu', $menu);
    API::finish();
}

?>
