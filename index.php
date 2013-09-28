<?php
// preliminaries
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];

require_once('include/errorhandler.class.php');
require_once('include/log.class.php');
require_once('include/common.class.php');
require_once('include/hooks.class.php');

ErrorHandler::initialize(EXPLICIT);
Common::setCaching(true);
Common::setMinifying(true);
Log::setLevel(VERBOSE);
Log::setDirectory('logs/');

if (ErrorHandler::getLevel() == DISCREET)
	Hooks::attach('error', function() {
		echo 'ERROR';
		exit;
	});


// form the request URI
$base_url = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1); // remove filename

$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/') !== false)
	substr($request_uri, 0, strpos($request_uri, '?')); // remove query string

if (strncmp($base_url, $request_uri, strlen($base_url)))
	user_error('Base directory PHP_SELF does not equal the root directories of REQUEST_URL', ERROR);

$request_uri = substr($request_uri, strlen($base_url)); // remove basedir from URI
Log::request($_SERVER['REQUEST_URI']);

if (!Common::validUrl($request_uri))
	user_error('Request URI doesn\'t validate (' . $_SERVER['REQUEST_URI'] . ')', ERROR);


// redirect resources
if (strpos($request_uri, 'resources/') === 0 ||
	strpos($request_uri, 'themes/') === 0 ||
	strpos($request_uri, 'media/') === 0)
{
	$extensions_mime = array(
		'js' => 'application/x-javascript',
		'css' => 'text/css',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'htm' => 'text/html',
		'html' => 'text/html',
		'svg' => 'image/svg+xml',
		'eot' => 'application/vnd.ms-fontobject',
		'woff' => 'application/font-woff',
		'otf' => 'application/octet-stream',
		'ttf' => 'application/x-font-ttf'
	);

	$query_position = strrpos($request_uri, '?');
	if ($query_position !== false)
		$request_uri = substr($request_uri, 0, $query_position);

	$extension_position = strrpos($request_uri, '.');
	$extension = strtolower(substr($request_uri, $extension_position + 1));
	if ($extension_position !== false && array_key_exists($extension, $extensions_mime) && file_exists($request_uri))
	{
		Log::information('resource ' . $request_uri);
		if ($extension == 'png' || $extension == 'gif' || $extension == 'jpg' || $extension == 'jpeg')
		{
			$w = isset($_GET['w']) ? $_GET['w'] : 0;
			$h = isset($_GET['h']) ? $_GET['h'] : 0;
			$s = isset($_GET['s']) ? $_GET['s'] : 0;

			if ($w || $h || $s)
				$request_uri = Common::imageResize($request_uri, $w, $h, $s);
		}

		header('Content-Type: ' . $extensions_mime[$extension]);
		echo file_get_contents($request_uri);
	}
	elseif ($extension_position !== false && !array_key_exists($extension, $extensions_mime))
		user_error('Resource file extension "' . $extension . '" invalid of "' . $request_uri . '"', ERROR);
	else
		user_error('Could not find resource file "' . $request_uri . '"', ERROR);
	exit;
}

$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
Log::information('point A, common area: ' . number_format($endtime - $starttime, 4) . 's');


// not a resource, so start loading more stuff
session_start();

require_once('include/database.class.php');
require_once('include/security.php');
require_once('include/session.class.php');
require_once('include/form.class.php');
require_once('include/libs/smarty/Smarty.class.php');

$db = new Database('sqlite.db');
$bcrypt = new Bcrypt(8);

$smarty = new Smarty();
$smarty->setCompileDir('include/libs/smarty/templates_c/');
$smarty->setCacheDir('include/libs/smarty/cache/');
$smarty->setConfigDir('include/libs/smarty/configs/');
$smarty->addPluginsDir('include/smarty_plugins/');
$smarty->registerFilter('output', 'minify_html');

register_shutdown_function(function() {
	global $starttime, $db;

	$mtime = explode(' ', microtime());
	$endtime = $mtime[1] + $mtime[0];
	$totaltime = ($endtime - $starttime);

	Log::information('script took ' . number_format($endtime - $starttime, 4) . 's and ' . $db->queries() . ' queries');
});

$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
Log::information('point B, loading area: ' . number_format($endtime - $starttime, 4) . 's');


// setting more stuff
Hooks::clear('error');
require_once('include/dexterous.class.php');
require_once('hooks.php');

$uri = explode('/', substr($request_uri, 0, -1));
$domain_url = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')); // http
$domain_url .= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : ''); // s in https
$domain_url .= '://' . $_SERVER['SERVER_NAME'];
$domain_url .= ($_SERVER['SERVER_PORT'] == '80') ? '' : (':' . $_SERVER['SERVER_PORT']); // port

Dexterous::assign('uri', $uri);
Dexterous::assign('base_url', $base_url);
Dexterous::assign('domain_url', $domain_url);


// check whether database needs to be set up
if (file_exists($db->filename) === false)
	user_error('Database file never created at "' . $db->filename . '"', ERROR);
else if (filesize($db->filename) == 0)
	require_once('setup.php'); // until site is setup, this will exit!


$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
Log::information('point C, setting area: ' . number_format($endtime - $starttime, 4) . 's');


// start loading page

if ($uri[0] == 'admin' && Session::isUser()) {
	// ugly way to make sure that destroying modules has direct effect
	if (preg_match('/^admin\/modules\/(destroy\/|enable\/|disable\/)[a-zA-Z_][a-zA-Z0-9_]*\/$/', $request_uri))
	{
		if ($uri[2] == 'destroy') {
			if (file_exists('modules/' . $uri[3] . '/index.php') !== false) {
				$db->exec("DELETE FROM modules WHERE name = '" . $db->escape($uri[3]) . "';");
				include_once('modules/' . $uri[3] . '/index.php');
				if (function_exists($uri[3] . '_destroy') !== false)
					call_user_func($uri[3] . '_destroy');
				// TODO: remove module directory
			}
		} elseif ($uri[2] == 'enable') {
			$db->exec("UPDATE modules SET enabled = 1 WHERE name = '" . $db->escape($uri[3]) . "';");
		} elseif ($uri[2] == 'disable') {
			$db->exec("UPDATE modules SET enabled = 0 WHERE name = '" . $db->escape($uri[3]) . "';");
		}
	}

	Common::checkModules();
}

// load all modules
$module_names = array(); // is filled with enabled module (id => name) pairs
$modules = $db->query("SELECT * FROM modules WHERE enabled = 1;");
while ($module = $modules->fetch())
{
	$module_names[] = $module['name'];
	$module_hooks_filename = 'modules/' . $module['name'] . '/hooks.php';
	if (file_exists($module_hooks_filename) !== false)
		include_once($module_hooks_filename);
}

$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
Log::information('point D, module area: ' . number_format($endtime - $starttime, 4) . 's');


// set meta data
$theme_hooks_filename = '';
$header_settings = $db->query("SELECT * FROM settings;");
while ($header_setting = $header_settings->fetch())
	switch ($header_setting['key'])
	{
		case 'title':
			Dexterous::addTitle($header_setting['value']);
			if (strlen($header_setting['value']))
				Dexterous::assign('page_title', $header_setting['value']);
			break;
		case 'subtitle':
			if (strlen($header_setting['value']))
				Dexterous::assign('page_subtitle', $header_setting['value']);
			break;
		case 'description':
			if (strlen($header_setting['value']))
				Dexterous::assign('header_description', $header_setting['value']);
			break;
		case 'keywords':
			if (strlen($header_setting['value']))
				Dexterous::assign('header_keywords', $header_setting['value']);
			break;
		case 'theme':
			if ($theme = $db->querySingle("SELECT * FROM settings WHERE key = 'theme';"))
				$theme_hooks_filename = 'themes/' . $theme['value'] . '/hooks.php';
			break;

	}


// handle admin area
if ($uri[0] == 'admin')
	require_once('admin/admin.php'); // always exits

$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
$totaltime = ($endtime - $starttime);
Log::information('point E, pre-page: ' . number_format($endtime - $starttime, 4) . 's');


// show page
if ($link = $db->querySingle("SELECT * FROM links WHERE '" . $db->escape($request_uri) . "' REGEXP link LIMIT 1;"))
{
	$table = $db->query("SELECT * FROM `link_modules` WHERE link_id = '0' OR link_id = '" . $db->escape($link['id']) . "';");
	while ($row = $table->fetch())
	{
		$module_filename = 'modules/' . $row['module_name'] . '/index.php';
		if (in_array($row['module_name'], $module_names) && file_exists($module_filename) !== false)
			include_once($module_filename);
	}

	if (file_exists($theme_hooks_filename) !== false)
		include_once($theme_hooks_filename);

	Dexterous::addDeferredScript('resources/scripts/jquery.js');
	Dexterous::addDeferredScript('resources/scripts/common.js');

	Hooks::emit('header');

	Hooks::emit('module'); // get all module contents

	Hooks::emit('index');
	Hooks::emit('footer');
}
else
	user_error('uri with request "' . $request_uri . '" doesn\'t exist in database (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

?>
