<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

// enable/disable/clean links get handled in index.php so that they have direct effect

$modules = array();
$table = $db->query("SELECT * FROM module;");
while ($row = $table->fetch())
{
	if (($ini = parse_ini_file('modules/' . $row['module_name'] . '/config.ini')) !== false)
	{
		$row['title'] = Common::tryOrEmpty($ini['title']);
		$row['author'] = Common::tryOrEmpty($ini['author']);
		$row['description'] = Common::tryOrEmpty($ini['description']);
	}
	$modules[] = $row;
}

Core::addStyle('popbox.css');
Core::addStyle('dropdown.css');

Hooks::emit('admin_header');

Core::assign('modules', $modules);
Core::render('admin/modules.tpl');

Hooks::emit('admin_footer');
exit;

?>