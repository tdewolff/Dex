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
		$warnings[] = _('Apache module %s is not enabled', $module);

if (!extension_loaded('curl') && !preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen')))
	$warnings[] = _('Neither PHP module cURL is enabled nor PHP setting allow_url_fopen is true');

if (!$config->get('minifying'))
	$warnings[] = _('%s is disabled in %s', 'Minifying', 'dex.conf');
if (!$config->get('caching'))
	$warnings[] = _('%s is disabled in %s', 'Caching', 'dex.conf');
if (!$config->get('ssl'))
	$warnings[] = _('%s is disabled in %s', 'SSL login', 'dex.conf');
if ($config->get('verbose_logging'))
	$warnings[] = _('%s is disabled in %s', 'Verbose logging', 'dex.conf');
if ($config->get('display_errors'))
	$warnings[] = _('%s is disabled in %s', 'Displaying errors', 'dex.conf');
if ($config->get('display_notices'))
	$warnings[] = _('%s is disabled in %s', 'Displaying notices', 'dex.conf');

if (!is_writable('assets/'))
	$warnings[] = _('Directory %s is not writable', '"assets/"');
if (!is_writable('cache/'))
	$warnings[] = _('Directory %s is not writable', '"cache/"');
if (!is_writable('logs/'))
	$warnings[] = _('Directory %s is not writable', '"logs/"');

$handle = opendir('assets/');
while (($name = readdir($handle)) !== false)
	if (is_dir('assets/' . $name) && $name != '.' && $name != '..' && !is_writable('assets/' . $name))
		$warnings[] = _('Directory %s is not writable', '"assets/' . $name . '"');

Hooks::emit('admin-header');

Core::set('warnings', $warnings);
Core::render('admin/admin.tpl');

Hooks::emit('admin-footer');
exit;
