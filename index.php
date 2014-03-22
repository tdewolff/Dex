<?php

///////////////////
// preliminaries //

$starttime = explode(' ', microtime());
$config = is_file('config.ini') ? parse_ini_file('config.ini') : array();
if ($config === false) // for if parse_ini_file fails
	$config = array();

require_once('include/common.class.php');
require_once('include/error.class.php');
require_once('include/log.class.php');

Common::setMinifying(Common::tryOrDefault($config, 'minifying', true));
Common::ensureWritableDirectory('assets/');
Common::ensureWritableDirectory('cache/');
Common::ensureWritableDirectory('logs/');

Log::initialize();
Log::setVerbose(Common::tryOrDefault($config, 'verbose_logging', false));
Error::setDisplay(Common::tryOrDefault($config, 'display_errors', false));

// from here on all PHP errors are caught and handled correctly
register_shutdown_function(function() {
	$error = error_get_last();
	if (is_array($error))
		Error::report($error['type'], $error['message'], $error['file'], $error['line']);
});

// form the request URI
Log::request($_SERVER['REQUEST_URI']);

Common::$base_url = substr($_SERVER['PHP_SELF'], 1, strrpos($_SERVER['PHP_SELF'], '/')); // remove filename
Common::$base_url = preg_replace('/\/+$/', '/', Common::$base_url); // remove added slashes to base url

Common::$request_url = substr($_SERVER['REQUEST_URI'], 1); // get rid of front slash
if (strncmp(Common::$base_url, Common::$request_url, strlen(Common::$base_url)))
	user_error('Base directory PHP_SELF does not equal the root directories of REQUEST_URL', ERROR);

Common::$request_url = urldecode(substr(Common::$request_url, strlen(Common::$base_url))); // remove basedir from URI

$url = explode('/', Common::$request_url);
if (empty($url[count($url) - 1]))
	unset($url[count($url) - 1]);


// robots.txt and favicon.ico
if (Common::$request_url == 'robots.txt')
	Common::outputRobotsTxt(); // always exits
else if (Common::$request_url == 'favicon.ico')
	Common::outputFaviconIco();

require_once('include/resource.class.php'); // also needed for header.tpl (concatenateFiles())


///////////////
// resources //

if (Common::requestResource())
	require_once('res.php');


// pre-API; prevent session_start from blocking long-polling with console
if (Common::requestApi())
{
	require_once('include/api.class.php');

	API::load();
	if (API::action('console'))
	{
		require_once('include/console.class.php');

		if (Console::hasOutput())
			API::set('status', Console::getOutput());
		API::finish();
	}
}

require_once('include/security.php');
require_once('include/db.class.php');
require_once('include/user.class.php');

Bcrypt::setRounds(8);
Db::open('dex.db');

if (!session_start())
	user_error('Could not start session', ERROR);

register_shutdown_function(function() {
	global $starttime;

	$last_error = Db::lastError();
	if ($last_error)
		user_error('Database error "' . $last_error . '" occurred', ERROR);

	$endtime = explode(' ', microtime());
	$totaltime = ($endtime[1] + $endtime[0] - $starttime[1] - $starttime[0]);

	Log::notice('Script took ' . number_format($totaltime, 4) . 's and ' . Db::queries() . ' queries'); // can't use user_error since we're shutting down
});


// sitemap
if (Common::$request_url == 'sitemap.xml')
	Common::outputSitemapXml(); // always exits

// API; continued
if (Common::requestApi())
{
	$filename = API::expandUrl($url);
	if (!is_file($filename))
		user_error('Could not find API file "' . $filename . '"', ERROR);

	require_once($filename);
	user_error('API not handled in "' . $filename . '"', ERROR);
}


////////////////////////
// admin or site page //

ob_start('minifyHtml');

require_once('include/dex.class.php');
require_once('include/hooks.class.php'); // from here on all PHP errors gives an error page
require_once('include/stats.class.php');
require_once('core/hooks.php');

if (User::loggedIn())
{
	User::refreshLogin();
	Core::assign('username', User::getUsername());
	Core::assign('role', User::getRole());
}
else if (!Common::requestAdmin())
	Stats::registerPageVisit();

Core::assign('base_url', Common::$base_url);
Core::assign('session_time', SESSION_TIME);


// handle admin area
if (Common::requestAdmin())
	require_once('admin.php'); // always exits


// load all site ettings
$settings = array();
$table = Db::query("SELECT * FROM setting;");
while ($row = $table->fetch())
{
	$settings[$row['key']] = $row['value'];
	if (!empty($row['value']))
	   Core::assign('setting_' . $row['key'], $row['value']);
}

Core::addTitle($settings['title']);
Core::$theme_name = $settings['theme'];


// load page
$link = Db::querySingle("SELECT * FROM link WHERE '" . Db::escape(Common::$request_url) . "' REGEXP url or '/" . Db::escape(Common::$request_url) . "' = url LIMIT 1;");
if ($link)
{
	Core::addTitle($link['title']);
	Core::$link_id = $link['link_id'];
	Core::$template_name = $link['template_name'];

	Core::assign('link_id', $link['link_id']);
}


// load in admin bar
if (User::getTimeLeft() !== false)
{
	Core::addStyle('vendor/font-awesome.css');
	Core::addStyle('vendor/jquery-ui.css');
	Core::addStyle('vendor/fancybox.css');
	Core::addStyle('site-admin.css');
    Core::addStyle('dexedit.css');
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
	Core::addDeferredScript('site-admin.js');
    Core::addDeferredScript('dexedit.js');
}


// load in module hooks
$table = Db::query("SELECT * FROM link_module
	JOIN module ON link_module.module_name = module.module_name
	WHERE (link_id = '0'" . (isset($link['link_id']) ? " OR link_id = '" . Db::escape($link['link_id']) . "'" : "") . ") AND module.enabled = 1;");
while ($row = $table->fetch())
	include_once('modules/' . $row['module_name'] . '/hooks.php');


// load in theme
if (is_file('themes/' . Core::getThemeName() . '/hooks.php') !== false)
	include_once('themes/' . Core::getThemeName() . '/hooks.php');


// load in template
if (is_file('templates/' . Core::getTemplateName() . '/hooks.php') !== false)
	include_once('templates/' . Core::getTemplateName() . '/hooks.php');


// show page
if ($link)
	Hooks::emit('site');
else
	user_error('Page not found', ERROR);

?>