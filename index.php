<?php
// preliminaries
$starttime = explode(' ', microtime());

require_once('include/errorhandler.class.php');
require_once('include/log.class.php');
require_once('include/common.class.php');
require_once('include/hooks.class.php');

ErrorHandler::initialize(EXPLICIT);
Common::setCaching(true);
Common::setMinifying(false);
Log::setLevel(VERBOSE);
Log::setDirectory('logs/');

if (ErrorHandler::getLevel() == DISCREET)
	Hooks::attach('error', function() {
		echo 'ERROR';
		exit;
	});


// form the request URI
$base_url = substr($_SERVER['PHP_SELF'], 1, strrpos($_SERVER['PHP_SELF'], '/')); // remove filename
$request_uri = substr($_SERVER['REQUEST_URI'], 1); // get rid of front slash
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


// not a resource, so start loading more stuff
session_start();
ob_start(/*"minify_html"*/);

require_once('include/database.class.php');
require_once('include/security.php');
require_once('include/session.class.php');
require_once('include/form.class.php');

$db = new Database('sqlite.db');
$bcrypt = new Bcrypt(8);

register_shutdown_function(function() {
	global $starttime, $db;

	$endtime = explode(' ', microtime());
	$totaltime = ($endtime[1] + $endtime[0] - $starttime[1] - $starttime[0]);

	Log::information('script took ' . number_format($totaltime, 4) . 's and ' . $db->queries() . ' queries');
});


// setting more stuff
Hooks::clear('error');
require_once('include/dexterous.class.php');
require_once('hooks.php');

$uri = explode('/', substr($request_uri, 0, -1));
$domain_url = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')); // http
$domain_url .= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : ''); // s in https
$domain_url .= '://' . $_SERVER['SERVER_NAME'];
$domain_url .= ($_SERVER['SERVER_PORT'] == '80') ? '' : (':' . $_SERVER['SERVER_PORT']); // port
$domain_url .= '/';

Dexterous::assign('domain_url', $domain_url);
Dexterous::assign('base_url', $base_url);


// check whether database needs to be set up
if (file_exists($db->filename) === false)
	user_error('Database file never created at "' . $db->filename . '"', ERROR);
else if (filesize($db->filename) == 0)
	require_once('admin/setup.php'); // until site is setup, this will exit!


// start loading page
if ($uri[0] == 'admin' && Session::isUser())
{
	Common::checkModules();

	// ugly way to make sure that destroying modules has direct effect
	if (preg_match('/^admin\/modules\/(destroy\/|enable\/|disable\/)[a-zA-Z_][a-zA-Z0-9_]*\/$/', $request_uri))
		if ($uri[2] == 'destroy')
			if (file_exists('modules/' . $uri[3] . '/index.php') !== false)
			{
				$db->exec("DELETE FROM modules WHERE name = '" . $db->escape($uri[3]) . "';");
				include_once('modules/' . $uri[3] . '/index.php');
				if (function_exists($uri[3] . '_destroy') !== false)
					call_user_func($uri[3] . '_destroy');
				// TODO: remove module directory
			}
		elseif ($uri[2] == 'enable')
			$db->exec("UPDATE modules SET enabled = 1 WHERE name = '" . $db->escape($uri[3]) . "';");
		elseif ($uri[2] == 'disable')
			$db->exec("UPDATE modules SET enabled = 0 WHERE name = '" . $db->escape($uri[3]) . "';");
}


// load all settings
$site_settings = array();
$settings = $db->query("SELECT * FROM settings;");
while ($setting = $settings->fetch())
{
	$site_settings[$setting['key']] = $setting['value'];
	Dexterous::assign('settings_' . $setting['key'], $setting['value']);
}


// handle admin area
if ($uri[0] == 'admin')
	require_once('admin/admin.php'); // always exits


// show page
if ($link = $db->querySingle("SELECT * FROM links WHERE '" . $db->escape($request_uri) . "' REGEXP link LIMIT 1;"))
{
	$table = $db->query("SELECT * FROM link_modules WHERE link_id = '0' OR link_id = '" . $db->escape($link['id']) . "';");
	while ($row = $table->fetch())
	{
		$module_hooks_filename = 'modules/' . $row['module_name'] . '/hooks.php';
		$module = $db->querySingle("SELECT * FROM modules WHERE name = '" . $db->escape($row['module_name']) . "';");
		if ($module['enabled'] == '1' && file_exists($module_hooks_filename) !== false)
			include_once($module_hooks_filename);
	}

	$theme_hooks_filename = 'themes/' . $site_settings['theme'] . '/hooks.php';
	if (file_exists($theme_hooks_filename) !== false)
		include_once($theme_hooks_filename);

	Dexterous::addDeferredScript('resources/scripts/jquery.js');
	Dexterous::addDeferredScript('resources/scripts/common.js');

	Hooks::emit('header', array('link_id' => $link['id']));
	echo '</header>';

	echo '<nav class="navigation" role="navigation">';
	Hooks::emit('navigation', array('link_id' => $link['id']));
	echo '</nav>';

	echo '<article class="main" role="main">';
	Hooks::emit('main', array('link_id' => $link['id']));
	echo '</article>';

	echo '<footer>';
	Hooks::emit('footer', array('link_id' => $link['id']));
}
else
	user_error('Uri with request "' . $request_uri . '" doesn\'t exist in database (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

?>
