<?php

require_once('include/form.class.php');

Core::addTitle('Dexterous');
Core::addTitle('Admin panel');
Core::addStyle('include/normalize.min.css');
Core::addStyle('vendor/font-awesome.min.css');
Core::addStyle('vendor/jquery-ui-1.10.3.min.css');
Core::addStyle('vendor/fancybox.min.css');
Core::addStyle('admin.min.css');
Core::addScript('vendor/jquery-2.0.3.min.js');
Core::addScript('vendor/jquery-ui-1.10.3.min.js');
Core::addScript('vendor/jquery.fancybox.min.js');
Core::addScript('vendor/doT.min.js');
Core::addScript('include/slidein.min.js');
Core::addScript('include/api.min.js');
Core::addDeferredScript('admin.min.js');

// setup
if (filesize($db->filename) == 0)
    require_once('core/admin/setup.php'); // until site is setup, this will exit!

// logout
if (Session::isUser() && $request_url == 'admin/logout/')
	Session::logOut();

// login
if (!Session::isUser())
	require_once('core/admin/login.php'); // exits if not logged in
else // didn't go through login screen
	Core::checkModules();

if (isset($url[1]) && $url[1] == 'auxiliary' && isset($url[2]))
{
	if (!is_file('core/templates/admin/auxiliary/' . $url[2] . '.tpl'))
		user_error('Auxiliary "' . $url[2] . '" does not exist', ERROR);

	Core::render('admin/auxiliary/' . $url[2] . '.tpl');
	exit;
}

$admin_links = array();
$admin_links[] = array('name' => 'index',  'regex' => 'admin/(logs/)?',              'file' => 'core/admin/index.php',  'url' => 'admin/',        'icon' => 'icon-home',          'title' => 'Admin panel', 'admin_only' => 0);
$admin_links[] = array('name' => 'pages',  'regex' => 'admin/pages/([0-9]+/|new/)?', 'file' => 'core/admin/pages.php',  'url' => 'admin/pages/',  'icon' => 'icon-file-text-alt', 'title' => 'Pages',       'admin_only' => 0);
$admin_links[] = array('name' => 'assets', 'regex' => 'admin/assets/',               'file' => 'core/admin/assets.php', 'url' => 'admin/assets/', 'icon' => 'icon-picture',       'title' => 'Assets',      'admin_only' => 0);

$admin_links[] = array();

$modules = array();
$table = $db->query("SELECT * FROM module ORDER BY module_name ASC;");
while ($row = $table->fetch())
{
	$ini_filename = 'modules/' . $row['module_name'] . '/config.ini';
	if (is_file($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
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

$admin_links[] = array('name' => 'settings',  'regex' => 'admin/settings/',             'file' => 'core/admin/settings.php',  'url' => 'admin/settings/',  'icon' => 'icon-wrench',   'title' => 'Settings',  'admin_only' => 0);
$admin_links[] = array('name' => 'users',     'regex' => 'admin/users/([0-9]+/|new/)?', 'file' => 'core/admin/users.php',     'url' => 'admin/users/',     'icon' => 'icon-user',     'title' => 'Users',     'admin_only' => 1);
$admin_links[] = array('name' => 'modules',   'regex' => 'admin/modules/',              'file' => 'core/admin/modules.php',   'url' => 'admin/modules/',   'icon' => 'icon-sitemap',  'title' => 'Modules',   'admin_only' => 1);
$admin_links[] = array('name' => 'templates', 'regex' => 'admin/templates/',            'file' => 'core/admin/templates.php', 'url' => 'admin/templates/', 'icon' => 'icon-file-alt', 'title' => 'Templates', 'admin_only' => 1);
$admin_links[] = array('name' => 'themes',    'regex' => 'admin/themes/',               'file' => 'core/admin/themes.php',    'url' => 'admin/themes/',    'icon' => 'icon-adjust',   'title' => 'Themes',    'admin_only' => 0);
$admin_links[] = array('name' => 'database',  'regex' => 'admin/database/',             'file' => 'core/admin/database.php',  'url' => 'admin/database/',  'icon' => 'icon-hdd',      'title' => 'Database',  'admin_only' => 1);
$admin_links[] = array('name' => 'logout',    'regex' => 'admin/logout/',               'file' => 'core/admin/logout.php',    'url' => 'admin/logout/',    'icon' => 'icon-signout',  'title' => 'Log out',   'admin_only' => 0);

Core::assign('username', Session::getUsername());
Core::assign('permission', ucfirst(Session::getPermission()));
Core::assign('is_admin', Session::isAdmin());
Core::assign('admin_links', $admin_links);

foreach ($admin_links as $i => $admin_link)
	if (!empty($admin_link))
	{
		$admin_link['regex'] = preg_replace('/\//', '\/', $admin_link['regex']);
		if (preg_match('/^' . $admin_link['regex'] . '$/', $request_url))
		{
			Core::assign('current_admin_i', $i);

			if (!is_file($admin_link['file']))
				user_error('Admin file "' . $admin_link['file'] . '" does not exist', ERROR);

			require_once($admin_link['file']);
			break;
		}
	}

user_error('Could not find page at "' . $request_url . '"', ERROR);

?>
