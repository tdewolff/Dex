<?php

if (!User::loggedIn())
	user_error('Forbidden access', ERROR);

if (API::action('modify_menu'))
{
	if (!API::has('menu'))
		user_error('No menu set', ERROR);

	$menu = API::get('menu');

	$errors = array();
	foreach ($menu as $item)
		if (strlen($menu_item) > 20)
			$errors[] = array('link_id' => $item['link_id'], 'error' => 'Too long'); // TODO: implement at .tpl, inline form errors need refactoring first

	if (!count($errors))
	{
		Db::exec("DELETE FROM module_menu;");
		foreach ($menu as $i => $item)
		{
			// TODO: error handling for too long names
			Db::exec("INSERT INTO module_menu (link_id, position, level, name, enabled) VALUES (
				'" . Db::escape($item['link_id']) . "',
				'" . Db::escape($i) . "',
				'" . Db::escape($item['level']) . "',
				'" . Db::escape($item['name']) . "',
				'" . Db::escape($item['enabled']) . "'
			);");
		}
	}

	API::set('errors', $errors);
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
			$row['name'] = htmlspecialchars($row['title']);
			$row['title'] = htmlspecialchars($row['title']);
			$row['enabled'] = 1;
			$non_menu[] = $row;
		}
		else
		{
			$row['name'] = htmlspecialchars($row['name']);
			$row['title'] = htmlspecialchars($row['title']);
			$menu[] = $row;
		}
	}
	$menu = array_merge($menu, $non_menu); // non_menu items come last

	API::set('menu', $menu);
	API::finish();
}
