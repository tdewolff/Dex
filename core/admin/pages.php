<?php

if (!isset($url[2]))
{
	Core::addStyle('vendor/dropdown.css');

	Hooks::emit('admin-header');
	Core::render('admin/pages.tpl');
	Hooks::emit('admin-footer');
	exit;
}
else if ($url[2] == 'new')
{
	$form = new Form('page');

	$form->addSection(_('New page'), '');
	$form->addText('title', _('Title'), _('As displayed in the titlebar'), '', array('.*', 1, 25, _('Unknown error')));
	$form->addLinkUrl('url', _('Link'), _('Leave empty for homepage'));

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
	$form->addDropdown('template_name', _('Template'), _('Determine page type'), $templates);

	$form->addSeparator();

	$form->setSubmit('<i class="fa fa-asterisk"></i>&ensp;' . _('Create'));
	$form->setResponse('', _('Not created'));

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
