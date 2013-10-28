<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (Common::isMethod('PUT'))
{
    $data = Common::getMethodData();
    if (!isset($data['module_name']))
        user_error('No module name set', ERROR);

    // toggle enable/disable
    $db->exec("UPDATE module SET enabled = !enabled WHERE module_name = '" . $db->escape($data['module_name']) . "';");
    exit;
}

$modules = array();
$table = $db->query("SELECT * FROM module;");
while ($row = $table->fetch())
{
	if (($ini = parse_ini_file('modules/' . $row['module_name'] . '/config.ini')) !== false)
	{
		$row['title'] = Common::tryOrEmpty($ini, 'title');
		$row['author'] = Common::tryOrEmpty($ini, 'author');
		$row['description'] = Common::tryOrEmpty($ini, 'description');
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