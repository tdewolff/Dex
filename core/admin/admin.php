<?php

Core::addTitle('Admin panel');
Core::addStyle('normalize.css');
Core::addStyle('font-awesome.css');
Core::addStyle('fancybox.css');
Core::addStyle('admin.css');
Core::addScript('jquery.js');
Core::addScript('fancybox.jquery.js');
Core::addScript('admin.js');
Core::addDeferredScript('ajax.defer.js');
Core::addDeferredScript('admin.defer.js');

// logout
if (Session::isUser() && $request_url == 'admin/logout/')
	Session::logOut();

// login
if (!Session::isUser())
	require_once('core/admin/login.php'); // exits if not logged in
else
	Core::checkModules();

$admin_links = array();
$admin_links[] = array('name' => 'index',    'regex' => 'admin/(index/(logs/(view|clear)/|cache/clear/))?', 'file' => 'index.php',    'url' => 'admin/',          'icon' => 'icon-home',    'title' => 'Admin panel', 'admin_only' => 0);
$admin_links[] = array('name' => 'settings', 'regex' => 'admin/settings/',                                  'file' => 'settings.php', 'url' => 'admin/settings/', 'icon' => 'icon-wrench',  'title' => 'Settings',    'admin_only' => 0);
$admin_links[] = array('name' => 'users',    'regex' => 'admin/users/([0-9]+/|new/)?',                      'file' => 'users.php',    'url' => 'admin/users/',    'icon' => 'icon-user',    'title' => 'Users',       'admin_only' => 1);
$admin_links[] = array('name' => 'assets',   'regex' => 'admin/assets/',                                    'file' => 'assets.php',   'url' => 'admin/assets/',   'icon' => 'icon-picture', 'title' => 'Assets',      'admin_only' => 0);

$admin_links[] = array();

$modules = array();
$table = $db->query("SELECT * FROM module;");
while ($row = $table->fetch())
	if (($ini = parse_ini_file('modules/' . $row['module_name'] . '/config.ini')) !== false)
		$admin_links[] = array(
			'name' => 'module_' . Common::tryOrEmpty($ini, 'name'),
			'regex' => Common::tryOrEmpty($ini, 'regex'),
			'file' => 'modules/' . $row['module_name'] . '/admin/' . Common::tryOrEmpty($ini, 'file'),
			'url' => Common::tryOrEmpty($ini, 'url'),
			'icon' => Common::tryOrEmpty($ini, 'icon'),
			'title' => Common::tryOrEmpty($ini, 'title'),
			'admin_only' => Common::tryOrEmpty($ini, 'admin_only'),
			'enabled' => $row['enabled']
		);

if (!empty($admin_links[count($admin_links) - 1]))
	$admin_links[] = array();

$admin_links[] = array('name' => 'modules',  'regex' => 'admin/modules/',  'file' => 'modules.php',  'url' => 'admin/modules/',  'icon' => 'icon-sitemap', 'title' => 'Modules',  'admin_only' => 1);
$admin_links[] = array('name' => 'themes',   'regex' => 'admin/themes/',   'file' => 'themes.php',   'url' => 'admin/themes/',   'icon' => 'icon-adjust',  'title' => 'Themes',   'admin_only' => 0);
$admin_links[] = array('name' => 'database', 'regex' => 'admin/database/', 'file' => 'database.php', 'url' => 'admin/database/', 'icon' => 'icon-hdd',     'title' => 'Database', 'admin_only' => 1);
$admin_links[] = array('name' => 'logout',   'regex' => 'admin/logout/',   'file' => 'logout.php',   'url' => 'admin/logout/',   'icon' => 'icon-signout', 'title' => 'Log out',  'admin_only' => 0);


Core::assign('isAdmin', Session::isAdmin());
Core::assign('admin_links', $admin_links);

$log_error = 'URL has no match with to any admin pages';
foreach ($admin_links as $i => $admin_link)
	if (!empty($admin_link))
	{
		$admin_link['regex'] = preg_replace('/\//', '\/', $admin_link['regex']);
		if (preg_match('/^' . $admin_link['regex'] . '$/', $request_url))
		{
			if (strpos($admin_link['file'], '/') === false)
				$admin_link['file'] = 'core/admin/' . $admin_link['file'];

			Core::assign('current_admin_i', $i);
			if (file_exists($admin_link['file']))
			{
				$log_error = 'error within admin page';
				require_once($admin_link['file']);
			}
			else
				$log_error = 'admin file "' . $admin_link['file'] . '" does not exist';
			break;
		}
	}

user_error($log_error . ' (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

?>
