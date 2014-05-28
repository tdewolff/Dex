<?php

if (!isset($url[2]))
{
	Core::addStyle('vendor/dropdown.css');

	Hooks::emit('admin-header');
	Core::render('admin/pages.tpl');
	Hooks::emit('admin-footer');
	exit;
}
else if ($url[2] != 'new')
{
	$link = Db::singleQuery("SELECT * FROM link WHERE link_id = '" . Db::escape($url[2]) . "' LIMIT 1");
	if (!$link)
		user_error('Page with link_id "' . $url[2] . '" not found', ERROR);

	if (!is_file('templates/' . $link['template_name'] . '/admin/index.php'))
		user_error('Template admin page "templates/' . $link['template_name'] . '/admin/index.php" not found', ERROR);

	Core::setTemplateName($link['template_name']);
	require_once('templates/' . $link['template_name'] . '/admin/index.php');
}
else
{
	$form = new Form('page');

	$form->addSection(__('New page'), '');
	$form->addText('title', __('Title'), __('As displayed in the titlebar'), '', array('.*', 1, 25, __('Unknown error')));
	$form->addLinkUrl('url', __('Link'), __('Leave empty for homepage'));

	$form->setId('title', 'url-feed');
	$form->setId('url', 'url');

	$templates = array();
	$handle = opendir('templates/');
	while (($template_name = readdir($handle)) !== false)
		if (is_dir('templates/' . $template_name) && $template_name != '.' && $template_name != '..')
		{
			$config = new Config('templates/' . $template_name . '/template.conf');
			$templates[$template_name] = $config->get('title');
		}
	$form->addDropdown('template_name', __('Template'), __('Determine page type'), $templates);

	$form->addSeparator();

	$form->setSubmit('<i class="fa fa-asterisk"></i>&ensp;' . __('Create'));
	$form->setResponse('', __('Not created'));

	if ($form->submitted())
	{
		if ($form->validate())
			if (($error = Core::verifyLinkUrl($form->get('url'))) !== true)
				 $form->setError('url', $error);
			else
			{
				$link_id = 0;
				$link = Db::singleQuery("SELECT link_id, title FROM link WHERE url = '" . Db::escape($form->get('url')) . "' LIMIT 1");
				if ($link)
				{
					if ($form->get('title') != $link['title'])
						Db::exec("
							UPDATE link SET
								title = '" . Db::escape($form->get('title')) . "',
								template_name = '" . Db::escape($form->get('template_name')) . "'
							WHERE link_id = '" . Db::escape($link['link_id']) . "';");
					$link_id = $link['link_id'];
				}
				else
				{
					Db::exec("
						INSERT INTO link (url, title, template_name) VALUES (
							'" . Db::escape($form->get('url')) . "',
							'" . Db::escape($form->get('title')) . "',
							'" . Db::escape($form->get('template_name')) . "'
						);");
					$link_id = Db::lastId();
				}
				$form->setRedirect('/' . Common::$base_url . 'admin/pages/');
			}
		$form->finish();
	}

	Hooks::emit('admin-header');

	Core::set('page', $form);
	Core::render('admin/page.tpl');

	Hooks::emit('admin-footer');
	exit;
}
