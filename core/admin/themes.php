<?php

if (isset($url[2]) && $url[2] == 'use' && isset($url[3]))
	$db->exec("UPDATE setting SET value = '" . $db->escape($url[3]) . "' WHERE key = 'theme';");

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
if ($theme = $db->querySingle("SELECT * FROM setting WHERE key = 'theme';"))
	$current_theme = $theme['value'];

Core::addStyle('popbox.css');
Core::addStyle('dropdown.css');

Hooks::emit('admin_header');

Core::assign('current_theme', $current_theme);
Core::assign('themes', $themes);
Core::render('admin/themes.tpl');

Hooks::emit('admin_footer');
exit;

?>
