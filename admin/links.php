<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (!isset($uri[2]) || $uri[2] == 'remove')
{
	if (isset($uri[2]) && $uri[2] == 'remove' && isset($uri[3]))
		$db->exec("DELETE FROM `links` WHERE id = '" . $db->escape($uri[3]) . "';");

	$links = array();
	$table = $db->query("SELECT * FROM `links`;");
	while ($row = $table->fetch())
	{
		$module_names = array();
		$table_link_module = $db->query("SELECT * FROM `link_modules` WHERE link_id = '" . $db->escape($row['id']) . "';");
		while ($row_link_module = $table_link_module->fetch())
		{
			$ini_filename = "modules/" . $row_link_module['module_name'] . "/" . $row_link_module['module_name'] . ".ini";
			if ($row_link_module['module_name'] == '0')
				$module_names[] = '- None -';
			else if (file_exists($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
				$module_names[] = $ini['title'];
			else
				$module_names[] = $row_link_module['module_name'];
		}

		$row['module_names'] = implode(', ', $module_names);
		$links[] = $row;
	}

	Dexterous::addStyle('resources/styles/popbox.css');
	Dexterous::addStyle('resources/styles/dropdown.css');
	Dexterous::addDeferredScript('resources/scripts/popbox.js');
	Dexterous::addDeferredScript('resources/scripts/dropdown.js');

	Hooks::emit('header');

	Dexterous::assign('links', $links);
	Dexterous::render('admin/links.tpl');

	Hooks::emit('footer');
	exit;
}
else
{
	$dropbox_modules = array('0' => '- None -');
	$modules = $db->query("SELECT * FROM `modules`;");
	while ($module = $modules->fetch()) {
		$ini_filename = "modules/" . $module['name'] . "/" . $module['name'] . ".ini";
		if (file_exists($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
			$dropbox_modules[$module['name']] = $ini['title'];
		else
			$dropbox_modules[$module['name']] = '(' . $module['name'] . ')';
	}

	$form = new Form('link', 'Edit link');
	$form->addSection('Link', 'Every URL typed in the address bar is processed and the correct module is loaded. Below you can define what module is loaded when the specified link is requested. Make sure the link is meaningful since its valuable for users and search engines.');
	$form->addText('page_link', 'Link', $domain_url . $base_url, array('([a-zA-Z0-9\s_\\\\\/\[\]\(\)\|\?\+\-\*\{\},:\^=!\<\>#\$]*\/)?', 0, 50, 'Must be valid link and end with /'));
	$form->addText('page_title', 'Title', 'As displayed in links and titlebar', array('[a-zA-Z0-9\s_\/\-]*', 0, 20, 'May contain alphanumeric characters, spaces and (_/-)'));
	//$form->addDropdown('module_name', 'Module', 'Module to load', $dropbox_modules);
	//$form->addParameters('module_params', 'Parameters', '');

	$form->addSeparator();
	$form->addSubmit('link', '<i class="icon-save"></i>&ensp;Save');

	if ($form->submittedBy('link'))
	{
		if ($form->verifyPost())
		{
            if ($db->querySingle("SELECT * FROM `links` WHERE link = '" . $db->escape($form->get('page_link')) . "' AND id != '" . $db->escape($uri[2]) . "' LIMIT 1;"))
                $form->setError('page_link', 'Already used');
            else if (substr($form->get('link'), 0, 6) == 'admin/')
                $form->setError('link', 'Cannot start with "admin/"');
            else
            {
				if ($uri[2] != 'new')
				{
					$db->exec("
					UPDATE `links` SET
						link = '" . $db->escape($form->get('page_link')) . "',
						title = '" . $db->escape($form->get('page_title')) . "'
					WHERE id = '" . $db->escape($uri[2]) . "';");
				}
				else
				{
					$db->exec("
					INSERT INTO `links` (link, title, module_name, module_params) VALUES (
						'" . $db->escape($form->get('page_link')) . "',
						'" . $db->escape($form->get('page_title')) . "'
					);");

					Dexterous::assign('form_action', $base_url . 'admin/links/' . $db->last_id() . '/');
				}

				$form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
			}
		}
		$form->postToSession();

		if ($uri[2] != 'new')
			Dexterous::assign('view', $form->get('page_link'));
	}
	else
	{
		if ($uri[2] != 'new')
		{
			$link = $db->querySingle("SELECT * FROM `links` WHERE id = '" . $db->escape($uri[2]) . "' LIMIT 1;");
			if ($link === false)
				Hooks::emit('error', 404);

			$form->set('page_link', $link['link']);
			$form->set('page_title', $link['title']);

			Dexterous::assign('view', $link['link']);
		}
	}

	Hooks::emit('header');

	$form->sessionToForm();
	$form->setupForm($smarty);

	Dexterous::render('admin/link.tpl');

	Hooks::emit('footer');
	exit;
}

?>
