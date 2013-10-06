<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

// enable/disable/deinstall links get handled in index.php so that they have direct effect

$modules = array();
$table = $db->query("SELECT * FROM modules;");
while ($row = $table->fetch())
{
	$ini_filename = 'modules/' . $row['name'] . '/config.ini';
	if (file_exists($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
	{
		$row['title'] = Common::tryOrEmpty($ini['title']);
		$row['author'] = Common::tryOrEmpty($ini['author']);
		$row['description'] = Common::tryOrEmpty($ini['description']);
	}
	else
	{
		$row['title'] = '(' . $row['name'] . ')';
		$row['author'] = '';
		$row['description'] = '';
	}
	$modules[] = $row;
}

Dexterous::addStyle('resources/styles/popbox.css');
Dexterous::addStyle('resources/styles/dropdown.css');
Dexterous::addDeferredScript('resources/scripts/popbox.js');
Dexterous::addDeferredScript('resources/scripts/dropdown.js');

Hooks::emit('admin_header');

Dexterous::assign('modules', $modules);
Dexterous::render('admin/modules.tpl');

Hooks::emit('admin_footer');
exit;

?>