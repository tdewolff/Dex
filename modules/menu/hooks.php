<?php

Hooks::attach('navigation', 0, function () {
    Module::setModuleName('menu');

    $menu = array();
    $non_menu = array();
    $table = Db::query("SELECT *, link.link_id AS link_id FROM link
        LEFT JOIN module_menu ON link.link_id = module_menu.link_id
        ORDER BY module_menu.position ASC;");
    while ($row = $table->fetch())
    {
        $row['url'] = '/' . Common::$base_url . $row['url'];
        $row['selected'] = ($link_id == $row['link_id'] ? '1' : '0');

        if (!isset($row['module_menu_id']))
        {
            $row['level'] = 0;
            $row['name'] = htmlspecialchars($row['title']);
            $row['enabled'] = 1;
            $non_menu[] = $row;
        }
        else
        {
            $row['name'] = htmlspecialchars($row['name']);
            $menu[] = $row;
        }
    }
    $menu = array_merge($menu, $non_menu); // non_menu items come last

    Module::set('menu', $menu);
    Module::render('index.tpl');
});

?>