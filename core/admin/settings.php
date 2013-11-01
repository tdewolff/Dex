<?php

$form = new Form('settings');
$form->addSection('Metadata', 'Site metadata is important for search engine indexing');
$form->addText('title', 'Site title', 'Displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 25, 'May contain alphanumeric characters and spaces'));
$form->addMultilineText('subtitle', 'Site subtitle', 'Displayed in the header', '', array('(.|\n)*', 0, 200, 'Unknown error'));
$form->addText('description', 'Site description', 'Describe the site concisely', '', array('.*', 0, 80, 'Unknown error'));
$form->addArray('keywords', 'Site keywords', '', array('.*', 0, 80, 'Unknown error'));

$form->addSeparator();
$form->addSubmit('settings', '<i class="icon-save"></i>&ensp;Save', '<span class="passed_time">(saved <span></span>)</span>', '(not saved)');

if ($form->submittedBy('settings'))
{
	if ($form->validateInput())
	{
		$db->exec("UPDATE setting SET value = '" . $db->escape($form->get('title')) . "' WHERE key = 'title';");
		$db->exec("UPDATE setting SET value = '" . $db->escape($form->get('subtitle')) . "' WHERE key = 'subtitle';");
		$db->exec("UPDATE setting SET value = '" . $db->escape($form->get('description')) . "' WHERE key = 'description';");
		$db->exec("UPDATE setting SET value = '" . $db->escape($form->get('keywords')) . "' WHERE key = 'keywords';");
	}
	$form->returnJSON();
}

$settings = $db->query("SELECT * FROM setting;");
while ($setting = $settings->fetch())
	$form->set($setting['key'], $setting['value']);

Hooks::emit('admin_header');

Core::assign('settings', $form);
Core::render('admin/settings.tpl');

Hooks::emit('admin_footer');
exit;

?>
