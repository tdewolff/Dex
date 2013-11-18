<?php

Core::addTitle('Admin panel');
Core::addStyle('include/normalize.css');
Core::addStyle('vendor/font-awesome.css');
Core::addStyle('vendor/fancybox.css');
Core::addStyle('admin.css');
Core::addScript('vendor/jquery-2.0.3.min.js');
Core::addScript('vendor/doT.min.js');
Core::addScript('include/api.js');
Core::addScript('include/slidein.js');
Core::addDeferredScript('vendor/jquery.fancybox.min.js');
Core::addDeferredScript('admin.js');

// logout
if (Session::isUser() && $request_url == 'admin/logout/')
	Session::logOut();

// login
if (!Session::isUser())
	require_once('core/admin/login.php'); // exits if not logged in
else
	Core::checkModules();

$admin_links = array();
$admin_links[] = array('name' => 'index',    'regex' => 'admin/(logs/)?',              'file' => 'index.php',    'url' => 'admin/',          'icon' => 'icon-home',          'title' => 'Admin panel', 'admin_only' => 0);
$admin_links[] = array('name' => 'settings', 'regex' => 'admin/settings/',             'file' => 'settings.php', 'url' => 'admin/settings/', 'icon' => 'icon-wrench',        'title' => 'Settings',    'admin_only' => 0);
$admin_links[] = array('name' => 'pages',    'regex' => 'admin/pages/([0-9]+/|new/)?', 'file' => 'pages.php',    'url' => 'admin/pages/',    'icon' => 'icon-file-text-alt', 'title' => 'Pages',       'admin_only' => 0);
$admin_links[] = array('name' => 'assets',   'regex' => 'admin/assets/',               'file' => 'assets.php',   'url' => 'admin/assets/',   'icon' => 'icon-picture',       'title' => 'Assets',      'admin_only' => 0);

$admin_links[] = array();

$modules = array();
$table = $db->query("SELECT * FROM module WHERE enabled = '1';");
while ($row = $table->fetch())
{
	$ini_filename = 'modules/' . $row['module_name'] . '/config.ini';
	if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
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
}

if (!empty($admin_links[count($admin_links) - 1]))
	$admin_links[] = array();

$admin_links[] = array('name' => 'users',     'regex' => 'admin/users/([0-9]+/|new/)?', 'file' => 'users.php',     'url' => 'admin/users/',     'icon' => 'icon-user',     'title' => 'Users',     'admin_only' => 1);
$admin_links[] = array('name' => 'modules',   'regex' => 'admin/modules/',              'file' => 'modules.php',   'url' => 'admin/modules/',   'icon' => 'icon-sitemap',  'title' => 'Modules',   'admin_only' => 1);
$admin_links[] = array('name' => 'templates', 'regex' => 'admin/templates/',            'file' => 'templates.php', 'url' => 'admin/templates/', 'icon' => 'icon-file-alt', 'title' => 'Templates', 'admin_only' => 1);
$admin_links[] = array('name' => 'themes',    'regex' => 'admin/themes/',               'file' => 'themes.php',    'url' => 'admin/themes/',    'icon' => 'icon-adjust',   'title' => 'Themes',    'admin_only' => 0);
$admin_links[] = array('name' => 'database',  'regex' => 'admin/database/',             'file' => 'database.php',  'url' => 'admin/database/',  'icon' => 'icon-hdd',      'title' => 'Database',  'admin_only' => 1);
$admin_links[] = array('name' => 'logout',    'regex' => 'admin/logout/',               'file' => 'logout.php',    'url' => 'admin/logout/',    'icon' => 'icon-signout',  'title' => 'Log out',   'admin_only' => 0);


Core::assign('isAdmin', Session::isAdmin());
Core::assign('admin_links', $admin_links);

$log_error = 'URL "' . $request_url . '"" has no match with to any admin pages';
foreach ($admin_links as $i => $admin_link)
	if (!empty($admin_link))
	{
		$admin_link['regex'] = preg_replace('/\//', '\/', $admin_link['regex']);
		if (preg_match('/^' . $admin_link['regex'] . '$/', $request_url))
		{
			Core::assign('apiUrl', '/' . $base_url . 'admin/api/' . substr($admin_link['file'], 0, -4) . '/');
			Core::assign('current_admin_i', $i);

			if (strpos($admin_link['file'], '/') === false)
				$admin_link['file'] = 'core/admin/' . $admin_link['file'];
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

user_error($log_error, ERROR);

?>
