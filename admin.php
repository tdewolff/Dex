<?php

require_once('include/form.class.php');

Core::addTitle('Dex');
Core::addTitle('Admin panel');
Core::addStyle('normalize.css');
Core::addStyle('vendor/font-awesome.css');
Core::addStyle('vendor/jquery-ui.css');
Core::addStyle('vendor/fancybox.css');
Core::addStyle('admin.css');
Core::addStyle('site-admin.css');
Core::addExternalScript('//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js');
Core::addExternalScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
Core::addDeferredScript('vendor/jquery.fancybox.min.js');
Core::addDeferredScript('vendor/jquery.iframe-transport.min.js');
Core::addDeferredScript('vendor/jquery.fileupload.min.js');
Core::addDeferredScript('vendor/jquery.knob.min.js');
Core::addDeferredScript('vendor/doT.min.js');
Core::addDeferredScript('api.js');
Core::addDeferredScript('upload.js');
Core::addDeferredScript('tooltips.js');
Core::addDeferredScript('admin.js');
Core::addDeferredScript('site-admin.js');

// setup
if (!Db::isValid())
	require_once('core/admin/setup.php'); // until site is setup, this will exit!

// logout
if (User::loggedIn() && Common::$request_url == 'admin/logout/')
	User::logOut();

// login
if (!User::loggedIn())
{
	if (strpos(Common::$request_url, 'admin/recover/') === 0)
		require_once('core/admin/recover.php'); // exits
	else
		require_once('core/admin/login.php'); // exits if not logged in
}
else // didn't go through login screen
	Core::checkModules();

if (Common::requestAdminAuxiliary() && isset($url[2]))
{
	if (!is_file('core/templates/admin/auxiliary/' . $url[2] . '.tpl'))
		user_error('Auxiliary "' . $url[2] . '" does not exist', ERROR);

	Core::render('admin/auxiliary/' . $url[2] . '.tpl');
	exit;
}

$_SESSION['last_admin_request'] = Common::$request_url;

$admin_links = array();
$admin_links[] = array('name' => 'index',  'regex' => 'admin/(r=.+|logs/)?',              'file' => 'core/admin/index.php',  'url' => 'admin/',        'icon' => 'fa-home',        'title' => 'Admin panel', 'admin_only' => 0);
$admin_links[] = array('name' => 'pages',  'regex' => 'admin/pages/([0-9]+/|new/)?', 'file' => 'core/admin/pages.php',  'url' => 'admin/pages/',  'icon' => 'fa-file-text-o', 'title' => 'Pages',       'admin_only' => 0);
$admin_links[] = array('name' => 'assets', 'regex' => 'admin/assets/',               'file' => 'core/admin/assets.php', 'url' => 'admin/assets/', 'icon' => 'fa-picture-o',   'title' => 'Assets',      'admin_only' => 0);

$admin_links[] = array();

$modules = array();
$table = Db::query("SELECT module_name, enabled FROM module ORDER BY module_name ASC;");
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

$admin_links[] = array();

$admin_links[] = array('name' => 'settings',  'regex' => 'admin/settings/',             'file' => 'core/admin/settings.php',  'url' => 'admin/settings/',  'icon' => 'fa-wrench',   'title' => 'Settings',       'admin_only' => 0);
$admin_links[] = array('name' => 'users',     'regex' => 'admin/users/([0-9]+/|new/)?', 'file' => 'core/admin/users.php',     'url' => 'admin/users/',     'icon' => 'fa-user',     'title' => 'Users',          'admin_only' => 1);
$admin_links[] = array('name' => 'modules',   'regex' => 'admin/modules/',              'file' => 'core/admin/modules.php',   'url' => 'admin/modules/',   'icon' => 'fa-sitemap',  'title' => 'Modules',        'admin_only' => 1);
$admin_links[] = array('name' => 'templates', 'regex' => 'admin/templates/',            'file' => 'core/admin/templates.php', 'url' => 'admin/templates/', 'icon' => 'fa-file-o',   'title' => 'Templates',      'admin_only' => 1);
$admin_links[] = array('name' => 'themes',    'regex' => 'admin/themes/',               'file' => 'core/admin/themes.php',    'url' => 'admin/themes/',    'icon' => 'fa-adjust',   'title' => 'Themes',         'admin_only' => 0);
$admin_links[] = array('name' => 'admin',     'regex' => 'admin/admin/',                'file' => 'core/admin/admin.php',     'url' => 'admin/admin/',     'icon' => 'fa-book',     'title' => 'Admin',          'admin_only' => 1);
$admin_links[] = array('name' => 'logout',    'regex' => 'admin/logout/',               'file' => 'core/admin/logout.php',    'url' => 'admin/logout/',    'icon' => 'fa-sign-out', 'title' => 'Log out',        'admin_only' => 0);

Core::set('admin_links', $admin_links);

foreach ($admin_links as $i => $admin_link)
	if (!empty($admin_link))
		if (preg_match('/^' . preg_replace('/\//', '\/', $admin_link['regex']) . '$/', Common::$request_url))
		{
			Core::set('current_admin_i', $i);

			if (!is_file($admin_link['file']))
				user_error('Admin file "' . $admin_link['file'] . '" does not exist', ERROR);

			require_once($admin_link['file']);
			break;
		}

user_error('Could not find page at "' . Common::$request_url . '"', ERROR);
