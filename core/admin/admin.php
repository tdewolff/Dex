<?php

if (!User::isAdmin())
{
	http_response_code(403);
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);
}

$warnings = array();

$apache_modules = apache_get_modules();
$apache_modules_needed = array('mod_deflate', 'mod_expires', 'mod_filter', 'mod_headers', 'mod_setenvif');
foreach ($apache_modules_needed as $module)
	if (!in_array($module, $apache_modules))
		$warnings[] = 'Apache module ' . $module . ' is not enabled';

if (!extension_loaded('curl') && !preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen')))
	$warnings[] = 'Neither PHP module cURL is enabled nor PHP setting allow_url_fopen is true';

if (!$config['minifying'])
	$warnings[] = 'Minifying is disabled in config.ini';
if (!$config['caching'])
	$warnings[] = 'Caching is disabled in config.ini';
if (!$config['ssl'])
	$warnings[] = 'SSL login is disabled in config.ini';
if ($config['verbose_logging'])
	$warnings[] = 'Verbose logging is enabled in config.ini';
if ($config['display_errors'])
	$warnings[] = 'Displaying errors is enabled in config.ini';
if ($config['display_notices'])
	$warnings[] = 'Displaying notices is enabled in config.ini';

if (!is_writable('assets/'))
	$warnings[] = 'Directory "assets/" is not writable';
if (!is_writable('cache/'))
	$warnings[] = 'Directory "cache/" is not writable';
if (!is_writable('logs/'))
	$warnings[] = 'Directory "logs/" is not writable';

$handle = opendir('assets/');
while (($name = readdir($handle)) !== false)
	if (is_dir('assets/' . $name) && $name != '.' && $name != '..' && !is_writable('assets/' . $name))
		$warnings[] = 'Directory "assets/' . $name . '" is not writable';

Hooks::emit('admin-header');

Core::set('warnings', $warnings);
Core::render('admin/admin.tpl');

Hooks::emit('admin-footer');
exit;
