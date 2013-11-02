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
$request_url = substr($_SERVER['REQUEST_URI'], 1); // get rid of front slash
if (strncmp($base_url, $request_url, strlen($base_url)))
	user_error('Base directory PHP_SELF does not equal the root directories of REQUEST_URL', ERROR);

$request_url = substr($request_url, strlen($base_url)); // remove basedir from URI
Log::request($_SERVER['REQUEST_URI']);

if (!Common::validUrl($request_url))
	user_error('Request URL doesn\'t validate (' . $_SERVER['REQUEST_URI'] . ')', ERROR);


// favicon.ico and robots.txt
if ($request_url == 'favicon.ico' || $request_url == 'robots.txt')
    if (file_exists($request_url))
    {
        if ($request_url == 'favicon.ico')
            header('Content-Type: image/x-icon');
        echo file_get_contents($request_url);
    }


// redirect resources
if (strpos($request_url, 'res/') === 0)
{
    $filename = Common::expandResourceUrl($request_url);
    if (empty($filename))
        user_error('Bad resource URL "' . $request_url . '"', ERROR);

    // remove querystring
    $querystring_position = strrpos($filename, '?');
    if ($querystring_position !== false)
        $filename = substr($filename, 0, $querystring_position);

    $extension_position = strrpos($filename, '.');
    $extension = strtolower(substr($filename, $extension_position + 1));

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

    if ($extension_position === false || !array_key_exists($extension, $extensions_mime))
        user_error('Resource file extension "' . $extension . '" invalid of "' . $request_url . '"', ERROR);
    else if (!file_exists($filename))
        user_error('Could not find resource file "' . $filename . '"', ERROR);
    else
    {
        // resize images
        if ($querystring_position !== false && ($extension == 'png' || $extension == 'gif' || $extension == 'jpg' || $extension == 'jpeg'))
        {
            $w = Common::tryOrZero($_GET, 'w');
            $h = Common::tryOrZero($_GET, 'h');
            $s = Common::tryOrZero($_GET, 's');
            $filename = Common::imageResize($filename, $w, $h, $s);
        }

        header('Content-Type: ' . $extensions_mime[$extension]);
        echo file_get_contents($filename);
    }
    exit;
}


// not a resource, so start loading more stuff
session_start();
ob_start('minifyHtml');

require_once('include/database.class.php');
require_once('include/security.php');
require_once('include/session.class.php');
require_once('include/form.class.php');

$db = new Database('database.sqlite3');
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
require_once('core/hooks.php');

$url = explode('/', substr($request_url, 0, -1));
$domain_url = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')); // http
$domain_url .= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : ''); // s in https
$domain_url .= '://' . $_SERVER['SERVER_NAME'];
$domain_url .= ($_SERVER['SERVER_PORT'] == '80') ? '' : (':' . $_SERVER['SERVER_PORT']); // port
$domain_url .= '/';

Core::assign('domain_url', $domain_url);
Core::assign('base_url', $base_url);


// check whether database needs to be set up
if (file_exists($db->filename) === false)
	user_error('Database file never created at "' . $db->filename . '"', ERROR);
else if (filesize($db->filename) == 0)
	require_once('core/admin/setup.php'); // until site is setup, this will exit!


// load all settings
$theme_name = '';
$settings = $db->query("SELECT * FROM setting;");
while ($setting = $settings->fetch())
{
    if (!empty($setting['value']))
	   Core::assign('setting_' . $setting['key'], $setting['value']);

    if ($setting['key'] == 'theme')
        $theme_name = $setting['value'];
}


// handle admin area
if ($url[0] == 'admin')
	require_once('core/admin/admin.php'); // always exits


// show page
if ($link = $db->querySingle("SELECT * FROM link WHERE '" . $db->escape($request_url) . "' REGEXP url LIMIT 1;"))
{
    Core::$link_id = $link['link_id'];
    Core::$template_name = $link['template_name'];

	// load in module hooks
	$table = $db->query("SELECT * FROM link_module
		JOIN module ON link_module.module_name = module.module_name
		WHERE (link_id = '0' OR link_id = '" . $db->escape($link['link_id']) . "') AND module.enabled = 1;");
	while ($row = $table->fetch())
		include_once('modules/' . $row['module_name'] . '/hooks.php');

	$theme_hooks_filename = 'themes/' . $theme_name . '/hooks.php';
	if (file_exists($theme_hooks_filename) !== false)
		include_once($theme_hooks_filename);


	Hooks::emit('header');
	echo '</header>';

	echo '<nav class="navigation" role="navigation">';
	Hooks::emit('navigation');
	echo '</nav>';

	echo '<article class="main" role="main">';
	Hooks::emit('main');
	echo '</article>';

	echo '<footer>';
	Hooks::emit('footer');
}
else
	user_error('Request URL "' . $request_url . '" doesn\'t exist in database (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

?>
