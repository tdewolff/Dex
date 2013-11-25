<?php

if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0)
{
    $s = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: http' . $s . '://' . substr($_SERVER['HTTP_HOST'], 4) . $_SERVER['REQUEST_URI']);
    exit();
}

// preliminaries
$starttime = explode(' ', microtime());
$config = file_exists('config.ini') ? parse_ini_file('config.ini') : array();

require_once('include/common.class.php');
require_once('include/error.class.php');
require_once('include/log.class.php');

Common::setMinifying(Common::tryOrDefault($config, 'minifying', true));
Common::ensureWritableDirectory('assets/');
Common::ensureWritableDirectory('cache/');

Log::setDirectory(Common::tryOrDefault($config, 'log_directory', 'logs/'));
Log::setVerbose(Common::tryOrDefault($config, 'verbose_logging', false));
Error::setDisplay(Common::tryOrDefault($config, 'display_errors', false));

register_shutdown_function(function() { // PHP errors
    $error = error_get_last();
    if ($error['type'] > 0)
        Error::report($error['type'], $error['message'], $error['file'], $error['line']);
});


// form the request URI
Log::request($_SERVER['REQUEST_URI']);

$base_url = substr($_SERVER['PHP_SELF'], 1, strrpos($_SERVER['PHP_SELF'], '/')); // remove filename
$base_url = preg_replace('/\/+$/', '/', $base_url); // remove added slashes to base url

$request_url = substr($_SERVER['REQUEST_URI'], 1); // get rid of front slash
if (strncmp($base_url, $request_url, strlen($base_url)))
	user_error('Base directory PHP_SELF does not equal the root directories of REQUEST_URL', ERROR);

$request_url = urldecode(substr($request_url, strlen($base_url))); // remove basedir from URI
$url = explode('/', $request_url);

if (!Common::validUrl($request_url))
	user_error('Request URL doesn\'t validate (' . $request_url . ')', ERROR);


// favicon.ico and robots.txt
if ($request_url == 'favicon.ico' || $request_url == 'robots.txt')
    if (file_exists($request_url))
    {
        if ($request_url == 'favicon.ico')
            header('Content-Type: image/x-icon');
        echo file_get_contents($request_url);
    }

require_once('include/resource.class.php'); // also needed for header.tpl (concatenateFiles())

// redirect resources
if (Common::requestResource())
{
    Resource::setCaching(Common::tryOrDefault($config, 'caching', true));

    $filename = Resource::expandUrl($url);

    // remove querystring
    $querystring_position = strrpos($filename, '?');
    if ($querystring_position !== false)
        $filename = substr($filename, 0, $querystring_position);

    // check extension
    $extension_position = strrpos($filename, '.');
    $extension = strtolower($extension_position === false ? '' : strtolower(substr($filename, $extension_position + 1)));

    if (!Resource::isResource($extension))
        user_error('Resource file extension "' . $extension . '" invalid of "' . $request_url . '"', ERROR);
    else if (!file_exists($filename))
        user_error('Could not find resource file "' . $filename . '"', ERROR);
    else
    {
        // resize images
        if ($querystring_position !== false && Resource::isImage($extension))
        {
            $w = Common::tryOrZero($_GET, 'w');
            $h = Common::tryOrZero($_GET, 'h');
            $s = Common::tryOrZero($_GET, 's');
            $filename = Resource::imageResize($filename, $w, $h, $s);
        }

        header('Content-Type: ' . Resource::getMime($extension));
        echo file_get_contents($filename);
    }
    exit;
}


// not a resource, so start loading more stuff
session_start();

require_once('include/database.class.php');
require_once('include/security.php');
require_once('include/session.class.php');

$db = new Database('database.sqlite3');
$bcrypt = new Bcrypt(8);

register_shutdown_function(function() {
	global $starttime, $db;

	$endtime = explode(' ', microtime());
	$totaltime = ($endtime[1] + $endtime[0] - $starttime[1] - $starttime[0]);

	Log::notice('script took ' . number_format($totaltime, 4) . 's and ' . $db->queries() . ' queries');
});


// API
if (Common::requestApi())
{
    require_once('include/api.class.php');

    API::load();
    $filename = API::expandUrl($url);
    if (!file_exists($filename))
        user_error('Could not find API file "' . $filename . '"', ERROR);

    require_once($filename);
    user_error('API not handled in "' . $filename . '"', ERROR);
}


// setting more stuff for a page
ob_start('minifyHtml');

require_once('include/dexterous.class.php');
require_once('include/form.class.php');
require_once('include/hooks.class.php');
require_once('core/hooks.php');

Session::refreshLogin();
Core::assign('base_url', $base_url);


// check whether database needs to be set up
if (file_exists($db->filename) === false)
	user_error('Database file never created at "' . $db->filename . '"', ERROR);
else if (filesize($db->filename) == 0)
	require_once('core/admin/setup.php'); // until site is setup, this will exit!


// handle admin area
if (Common::requestAdmin())
    require_once('core/admin/admin.php'); // always exits


// load all settings
$settings = array();
$table = $db->query("SELECT * FROM setting;");
while ($row = $table->fetch())
{
    $settings[$row['key']] = $row['value'];
    if (!empty($row['value']))
	   Core::assign('setting_' . $row['key'], $row['value']);
}


// show page
if ($link = $db->querySingle("SELECT * FROM link WHERE '" . $db->escape($request_url) . "' REGEXP url LIMIT 1;"))
{
    Core::$link_id = $link['link_id'];
    Core::$template_name = $link['template_name'];

    Core::addTitle($settings['title']);
    Core::addTitle($link['title']);

	// load in module hooks
	$table = $db->query("SELECT * FROM link_module
		JOIN module ON link_module.module_name = module.module_name
		WHERE (link_id = '0' OR link_id = '" . $db->escape($link['link_id']) . "') AND module.enabled = 1;");
	while ($row = $table->fetch())
		include_once('modules/' . $row['module_name'] . '/hooks.php');

	$theme_hooks_filename = 'themes/' . $settings['theme'] . '/hooks.php';
	if (file_exists($theme_hooks_filename) !== false)
		include_once($theme_hooks_filename);


    Hooks::emit('site-header');
    echo '<section class="page-wrapper">';

    echo '<header>';
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
    echo '</footer>';

    echo '</section>';
    Hooks::emit('site-footer');
}
else
	user_error('Request URL "' . $request_url . '" doesn\'t exist in database (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

?>