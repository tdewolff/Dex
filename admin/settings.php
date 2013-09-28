<?php

$form = new Form('settings', 'Settings');

$form->addSection('Metadata', 'Site metadata is important for search engine indexing');
$form->addText('title', 'Site title', 'Displayed in titlebar and header', array('[a-zA-Z0-9\s]*', 1, 25, 'May contain alphanumeric characters and spaces'));
$form->addText('subtitle', 'Site subtitle', 'Displayed in the header', array('[a-zA-Z0-9\s]*', 1, 50, 'May contain alphanumeric characters and spaces'));
$form->addText('description', 'Site description', 'Describe the site concisely', array('[a-zA-Z0-9\s,\.\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));
$form->addArray('keywords', 'Site keywords', 'Comma-separate tags', array('[a-zA-Z0-9\s,\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));

$form->addSeparator();
$form->addSubmit('settings', '<i class="icon-save"></i>&ensp;Save');

if ($form->submittedBy('settings'))
{
	if ($form->verifyPost())
	{
		$db->exec("UPDATE `settings` SET value = '" . $db->escape($form->get('title')) . "' WHERE key = 'title';");
		$db->exec("UPDATE `settings` SET value = '" . $db->escape($form->get('subtitle')) . "' WHERE key = 'subtitle';");
		$db->exec("UPDATE `settings` SET value = '" . $db->escape($form->get('description')) . "' WHERE key = 'description';");
		$db->exec("UPDATE `settings` SET value = '" . $db->escape($form->get('keywords')) . "' WHERE key = 'keywords';");

		$form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
	}
	$form->postToSession();
}
else
{
	$settings = $db->query("SELECT * FROM `settings`;");
	while ($setting = $settings->fetch())
		$form->set($setting['key'], $setting['value']);
}

Hooks::emit('header');

$form->sessionToForm();
$form->setupForm($smarty);

Dexterous::render('admin/settings.tpl');

Hooks::emit('footer');
exit;

?>
