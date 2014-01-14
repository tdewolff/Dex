<?php

// ensure www. is omitted
if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0)
{
    $s = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: http' . $s . '://' . substr($_SERVER['HTTP_HOST'], 4) . $_SERVER['REQUEST_URI']);
    exit();
}


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
if (empty($url[count($url) - 1]))
    unset($url[count($url) - 1]);

// TODO: remove?
//if (!Common::validUrl($request_url))
//    user_error('Request URL doesn\'t validate (' . $request_url . ')', ERROR);


// robots.txt and favicon.ico
if ($request_url == 'robots.txt')
    Common::outputRobotsTxt(); // always exits
else if ($request_url == 'favicon.ico')
    Common::outputFaviconIco();

require_once('include/resource.class.php'); // also needed for header.tpl (concatenateFiles())


///////////////
// resources //
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
    else if (!is_file($filename))
        user_error('Could not find resource file "' . $filename . '"', ERROR);
    else
    {
        if (Resource::isImage($extension))
        {
            if (is_file(Common::insertMinExtension($filename)) && filemtime($filename) < filemtime(Common::insertMinExtension($filename)))
                $filename = Common::insertMinExtension($filename);

            if ($querystring_position !== false)
            {
                // resize images
                $w = Common::tryOrZero($_GET, 'w');
                $h = Common::tryOrZero($_GET, 'h');
                $s = Common::tryOrZero($_GET, 's');
                $filename = Resource::imageResize($filename, $w, $h, $s);
            }
        }

        header('Content-Type: ' . Resource::getMime($extension));
        echo file_get_contents($filename);
    }
    exit;
}


////////////////////
// not a resource //

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

require_once('include/database.class.php');
require_once('include/security.php');
require_once('include/user.class.php');

// copy database if one exists but other doesn't
if ((!is_file('current.db') || filesize('current.db') == 0) && is_file('develop.db'))
    copy('develop.db', 'current.db');
else if (is_file('current.db') && (!is_file('develop.db') || filesize('develop.db') == 0))
    copy('current.db', 'develop.db');

Bcrypt::setRounds(8);

$db = new Database('develop.db');
if (is_file($db->filename) === false)
    user_error('Database file never created at "' . $db->filename . '"', ERROR);

session_start();
if (!User::loggedIn() && filesize('develop.db') != 0) // User::loggedIn() needs a database (develop) loaded
{
    $db = new Database('current.db');
    if (is_file($db->filename) === false)
        user_error('Database file never created at "' . $db->filename . '"', ERROR);
}


// sitemap
if ($request_url == 'sitemap.xml')
    Common::outputSitemapXml(); // always exits


register_shutdown_function(function() {
    global $starttime, $db;

    $endtime = explode(' ', microtime());
    $totaltime = ($endtime[1] + $endtime[0] - $starttime[1] - $starttime[0]);

    Log::notice('script took ' . number_format($totaltime, 4) . 's and ' . $db->queries() . ' queries');
});


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
//else
    Stats::registerPageVisit();

Core::assign('base_url', $base_url);


// handle admin area
if (Common::requestAdmin())
    require_once('core/admin/admin.php'); // always exits


// load all site ettings
$settings = array();
$table = $db->query("SELECT * FROM setting;");
while ($row = $table->fetch())
{
    $settings[$row['key']] = $row['value'];
    if (!empty($row['value']))
       Core::assign('setting_' . $row['key'], $row['value']);
}

Core::addTitle($settings['title']);


// load page
$link = $db->querySingle("SELECT * FROM link WHERE '" . $db->escape($request_url) . "' REGEXP url or '/" . $db->escape($request_url) . "' = url LIMIT 1;");
if ($link)
{
    Core::addTitle($link['title']);
    Core::$link_id = $link['link_id'];
    Core::$template_name = $link['template_name'];
}


// load in admin bar
if (User::loggedIn())
{
    Core::addStyle('vendor/font-awesome.css');
    Core::addStyle('vendor/fancybox.css');
    Core::addStyle('api.css');
    Core::addStyle('admin-bar.css');
    Core::addDeferredExternalScript('//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js');
    Core::addDeferredScript('vendor/jquery.fancybox.min.js');
    Core::addDeferredScript('api.js');
    Core::addDeferredScript('admin-bar.js');
}


// load in module hooks
$table = $db->query("SELECT * FROM link_module
    JOIN module ON link_module.module_name = module.module_name
    WHERE (link_id = '0'" . (isset($link['link_id']) ? " OR link_id = '" . $db->escape($link['link_id']) . "'" : "") . ") AND module.enabled = 1;");
while ($row = $table->fetch())
    include_once('modules/' . $row['module_name'] . '/hooks.php');


// load in theme
$theme_hooks_filename = 'themes/' . $settings['theme'] . '/hooks.php';
if (is_file($theme_hooks_filename) !== false)
    include_once($theme_hooks_filename);


// show page
if ($link)
    Hooks::emit('site');
else
    user_error('Page not found', ERROR);

?>
