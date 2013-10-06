<?php

if (isset($uri[2]) && $uri[2] == 'use' && isset($uri[3]))
	$db->exec("UPDATE settings SET value = '" . $db->escape($uri[3]) . "' WHERE key = 'theme';");

if (isset($uri[2]) && $uri[2] == 'destroy' && isset($uri[3]))
	;// TODO: remove files

$themes = array();
$handle = opendir('themes/');
while (($theme_name = readdir($handle)) !== false)
	if (is_dir('themes/' . $theme_name) && $theme_name != '.' && $theme_name != '..')
	{
		$ini_filename = 'themes/' . $theme_name . '/config.ini';
		if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
			$themes[] = array(
				'name' => $theme_name,
				'title' => isset($ini['title']) ? $ini['title'] : '',
		 		'author' => isset($ini['author']) ? $ini['author'] : ''
			);
	}

$current_theme = '';
if ($theme = $db->querySingle("SELECT * FROM settings WHERE key = 'theme';"))
	$current_theme = $theme['value'];

Dexterous::addStyle('resources/styles/popbox.css');
Dexterous::addStyle('resources/styles/dropdown.css');
Dexterous::addDeferredScript('resources/scripts/popbox.js');
Dexterous::addDeferredScript('resources/scripts/dropdown.js');

Hooks::emit('admin_header');

Dexterous::assign('current_theme', $current_theme);
Dexterous::assign('themes', $themes);
Dexterous::render('admin/themes.tpl');

Hooks::emit('admin_footer');
exit;

?>
