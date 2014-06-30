<?php

if (!User::isAdmin())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('get_templates'))
{
	$templates = array();
	if (($handle = opendir('templates/')) !== false)
		while (($template_name = readdir($handle)) !== false)
			if (is_dir('templates/' . $template_name) && $template_name != '.' && $template_name != '..')
			{
				Language::extend('templates', $template_name, Common::tryOrEmpty($dex_settings, 'language'));
				$config = new Config('templates/' . $template_name . '/template.conf');
				$templates[] = array(
					'name' => $template_name,
					'title' => $config->get('title'),
					'author' => $config->get('author'),
					'description' => ___('templates_' . $template_name, $config->get('description'))
				);
			}
	Common::sortOn($templates, 'name');

	API::set('templates', $templates);
	API::finish();
}
