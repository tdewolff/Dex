<?php

Dexterous::addTitle('Admin panel');
Dexterous::addStyle('resources/styles/normalize.css');
Dexterous::addStyle('resources/styles/admin.css');
Dexterous::addStyle('resources/styles/font-awesome.css');
Dexterous::addScript('resources/scripts/jquery.js');
Dexterous::addDeferredScript('resources/scripts/admin.js');

// login
if (!Session::isUser())
	require_once('admin/login.php'); // exits if not logged in
Dexterous::assign('isAdmin', Session::isAdmin());

// logout
if ($request_uri == 'admin/logout/')
{
	Session::logOut();
	require_once('admin/login.php');
}

$admin_links = array();
$admin_links[] = array('regex' => 'admin/(index/(logs/(view|clear)/|cache/clear/))?', 'file' => 'admin/index.php',    'uri' => 'admin/',          'icon' => 'icon-home',    'title' => 'Admin panel', 'admin_only' => 0);
$admin_links[] = array('regex' => 'admin/settings/',                                  'file' => 'admin/settings.php', 'uri' => 'admin/settings/', 'icon' => 'icon-wrench',  'title' => 'Settings',    'admin_only' => 0);
$admin_links[] = array('regex' => 'admin/users/([0-9]+/|new/|remove/[0-9]+/)?',       'file' => 'admin/users.php',    'uri' => 'admin/users/',    'icon' => 'icon-user',    'title' => 'Users',       'admin_only' => 1);
$admin_links[] = array('regex' => 'admin/media/',                                     'file' => 'admin/media.php',    'uri' => 'admin/media/',    'icon' => 'icon-picture', 'title' => 'Media',       'admin_only' => 0);

$admin_links[] = array();

$modules = array();
$table = $db->query("SELECT * FROM modules;");
while ($row = $table->fetch())
{
	$ini_filename = 'modules/' . $row['name'] . '/config.ini';
	if (file_exists($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
		$admin_links[] = array(
			'regex' => Common::tryOrEmpty($ini['regex']),
			'file' => Common::tryOrEmpty($ini['file']),
			'uri' => Common::tryOrEmpty($ini['uri']),
			'icon' => Common::tryOrEmpty($ini['icon']),
			'title' => Common::tryOrEmpty($ini['title']),
			'admin_only' => Common::tryOrEmpty($ini['admin_only']),
			'enabled' => $row['enabled']
		);
}

if (!empty($admin_links[count($admin_links) - 1]))
	$admin_links[] = array();

$admin_links[] = array('regex' => 'admin/modules/((destroy/|enable/|disable/)[a-zA-Z_][a-zA-Z0-9_]*/)?', 'file' => 'admin/modules.php', 'uri' => 'admin/modules/', 'icon' => 'icon-sitemap', 'title' => 'Modules', 'admin_only' => 1);
$admin_links[] = array('regex' => 'admin/themes/([a-zA-Z_][a-zA-Z0-9_]*/)?((use|destroy)/[a-zA-Z_][a-zA-Z0-9_]*/)?', 'file' => 'admin/themes.php', 'uri' => 'admin/themes/', 'icon' => 'icon-adjust', 'title' => 'Themes', 'admin_only' => 0);
//$admin_links[] = array('regex' => 'admin/links/([0-9]+/|new/|remove/[0-9]+/)?', 'file' => 'admin/links.php',    'uri' => 'admin/links/',  'icon' => 'icon-link',    'title' => 'Links',    'admin' => 1);
$admin_links[] = array('regex' => 'admin/sqlite/',                              'file' => 'admin/sqlite.php',   'uri' => 'admin/sqlite/', 'icon' => 'icon-hdd',     'title' => 'Database', 'admin_only' => 1);
$admin_links[] = array('regex' => 'admin/logout/',                              'file' => 'admin/logout.php',   'uri' => 'admin/logout/', 'icon' => 'icon-signout', 'title' => 'Log out',  'admin_only' => 0);

Dexterous::assign('admin_links', $admin_links);

$log_error = 'URI has no match with to any admin pages';
foreach ($admin_links as $i => $admin_link)
	if (!empty($admin_link))
	{
		$admin_link['regex'] = preg_replace('/\//', '\/', $admin_link['regex']);
		if (preg_match('/^' . $admin_link['regex'] . '$/', $request_uri))
		{
			Dexterous::assign('current_admin_i', $i);
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
