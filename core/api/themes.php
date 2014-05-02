<?php

if (!User::loggedIn())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('change_theme'))
{
	if (!API::has('theme_name'))
		user_error('No theme name set', ERROR);

	Db::exec("UPDATE setting SET value = '" . Db::escape(API::get('theme_name')) . "' WHERE key = 'theme';");
	API::finish();
}
else if (API::action('get_themes'))
{
	$themes = array();
	$handle = opendir('themes/');
	while (($theme_name = readdir($handle)) !== false)
		if (is_dir('themes/' . $theme_name) && $theme_name != '.' && $theme_name != '..')
		{
			$config = new Config('themes/' . $theme_name . '/theme.conf');
			$themes[] = array(
				'name' => $theme_name,
				'title' => $config->get('title'),
				'author' => $config->get('author'),
				'mtime' => filemtime('themes/' . $theme_name . '/resources/preview.png')
			);
		}
	Common::sortOn($themes, 'name');

	API::set('themes', $themes);
	API::finish();
}
