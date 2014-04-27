<?php

if (!User::isAdmin())
{
	Common::responseCode(403);
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);
}

$warnings = array();

$apache_modules = apache_get_modules();
$apache_modules_needed = array('mod_deflate', 'mod_expires', 'mod_filter', 'mod_headers', 'mod_setenvif');
foreach ($apache_modules_needed as $module)
	if (!in_array($module, $apache_modules))
		$warnings[] = __('Apache module %s is not enabled', $module);

if (!extension_loaded('curl') && !preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen')))
	$warnings[] = __('Neither PHP module cURL is enabled nor PHP setting allow_url_fopen is true');

Log::notice($dex_conf->get('minifying'));
if (!$dex_conf->get('minifying'))
	$warnings[] = __('%s is disabled in %s, %ssolve%s', 'Minifying', 'dex.conf', '<a href="#" data-warning="minifying">', '</a>');
if (!$dex_conf->get('caching'))
	$warnings[] = __('%s is disabled in %s, %ssolve%s', 'Caching', 'dex.conf', '<a href="#" data-warning="caching">', '</a>');
if (!$dex_conf->get('ssl'))
	$warnings[] = __('%s is disabled in %s, only solve if SSL is available', 'SSL login', 'dex.conf', '<a href="#" data-warning="ssl">', '</a>');
if ($dex_conf->get('verbose_logging'))
	$warnings[] = __('%s is disabled in %s, %ssolve%s', 'Verbose logging', 'dex.conf', '<a href="#" data-warning="verbose_logging">', '</a>');
if ($dex_conf->get('display_errors'))
	$warnings[] = __('%s is disabled in %s, %ssolve%s', 'Displaying errors', 'dex.conf', '<a href="#" data-warning="display_errors">', '</a>');
if ($dex_conf->get('display_notices'))
	$warnings[] = __('%s is disabled in %s, %ssolve%s', 'Displaying notices', 'dex.conf', '<a href="#" data-warning="display_notices">', '</a>');

if (!is_writable('assets/'))
	$warnings[] = __('Directory %s is not writable', '"assets/"');
if (!is_writable('cache/'))
	$warnings[] = __('Directory %s is not writable', '"cache/"');
if (!is_writable('logs/'))
	$warnings[] = __('Directory %s is not writable', '"logs/"');

$handle = opendir('assets/');
while (($name = readdir($handle)) !== false)
	if (is_dir('assets/' . $name) && $name != '.' && $name != '..' && !is_writable('assets/' . $name))
		$warnings[] = __('Directory %s is not writable', '"assets/' . $name . '"');

Hooks::emit('admin-header');

Core::set('warnings', $warnings);
Core::render('admin/admin.tpl');

Hooks::emit('admin-footer');
exit;
