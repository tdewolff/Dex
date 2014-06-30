<?php

if (!User::isAdmin())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('enable_module') || API::action('disable_module'))
{
	if (!API::has('module_name'))
		user_error('No module name set', ERROR);

	Db::exec("
	UPDATE module SET enabled = '" . Db::escape(API::action('enable_module') ? '1' : '0') . "'
	WHERE module_name = '" . Db::escape(API::get('module_name')) . "';");
	API::finish();
}
else if (API::action('reinstall_module'))
{
	if (!API::has('module_name') || !is_file('modules/' . API::get('module_name') . '/admin/setup.php'))
		user_error('No module name set or module doesn\'t exist', ERROR);

	include_once('modules/' . API::get('module_name') . '/admin/setup.php');
	API::finish();
}
else if (API::action('get_modules'))
{
	$modules = array();
	$table = Db::query("SELECT * FROM module ORDER BY module_name ASC;");
	while ($row = $table->fetch())
	{
		$config = new Config('modules/' . $row['module_name'] . '/module.conf');
		$row['module_id'] = count($modules);
		$row['title'] = $config->get('title');
		$row['author'] = $config->get('author');
		$row['description'] = ___('modules_' . $row['module_name'], $config->get('description'));
		$modules[] = $row;
	}
	API::set('modules', $modules);
	API::finish();
}
