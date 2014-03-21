<?php

if (!isset($url[2]))
{
	Core::addStyle('vendor/dropdown.css');

	Hooks::emit('admin-header');
	Core::render('admin/pages.tpl');
	Hooks::emit('admin-footer');
	exit;
}
else
{
	if ($url[2] != 'new')
		user_error('Page ID "' . $url[2] . '" doesn\'t exist', ERROR);

	$form = new Form('page');

	$form->addSection(($url[2] == 'new' ? 'New page' : 'Edit page'), '');
	$form->addText('title', 'Title', 'As displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 20, 'Only alphanumeric characters and spaces allowed'));
	$form->addLinkUrl('url', 'URL', 'Leave empty for homepage');

	$form->setId('title', 'url-feed');
	$form->setId('url', 'url');

	$templates = array();
	$handle = opendir('templates/');
	while (($template_name = readdir($handle)) !== false)
		if (is_dir('templates/' . $template_name) && $template_name != '.' && $template_name != '..')
		{
			$ini_filename = 'templates/' . $template_name . '/config.ini';
			if (is_file($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
				$templates[$template_name] = Common::tryOrEmpty($ini, 'title');
		}
	$form->addDropdown('template_name', 'Template', 'Determine page type', $templates);

	$form->addSeparator();

	$form->setSubmit('<i class="fa fa-asterisk"></i>&ensp;Create');
	$form->setResponse('Created page', 'Not created');

	if ($form->submitted())
	{
		if ($form->validate())
			if (($error = Core::verifyLinkUrl($form->get('url'))) !== true)
				 $form->setError('url', $error);
			else
			{
				$link_id = 0;
				$link = Db::querySingle("
					SELECT * FROM link WHERE url = '" . Db::escape($form->get('url')) . "' LIMIT 1");
				if ($link)
				{
					if ($form->get('title') != $link['title'])
						Db::exec("
							UPDATE link SET
								title = '" . Db::escape($form->get('title')) . "',
								template_name = '" . Db::escape($form->get('template_name')) . "',
								modify_time = '" . Db::escape(time()) . "'
							WHERE link_id = '" . Db::escape($link['link_id']) . "';");
					$link_id = $link['link_id'];
				}
				else
				{
					Db::exec("
						INSERT INTO link (url, title, template_name, modify_time) VALUES (
							'" . Db::escape($form->get('url')) . "',
							'" . Db::escape($form->get('title')) . "',
							'" . Db::escape($form->get('template_name')) . "',
							'" . Db::escape(time()) . "'
						);");
					$link_id = Db::lastId();
				}
				$form->setRedirect('/' . Common::$base_url . 'admin/pages/');
			}
		$form->finish();
	}

	Hooks::emit('admin-header');

	Core::assign('page', $form);
	Core::render('admin/page.tpl');

	Hooks::emit('admin-footer');
	exit;
}

?>
