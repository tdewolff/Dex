<?php

$form = new Form('settings');

$form->addSection('Settings', 'General site settings');
$form->addText('title', 'Title', 'Displayed in the titlebar and site header', 'CubicMelonFirm', array('[a-zA-Z0-9\s]*', 1, 25, 'Only alphanumeric characters and spaces allowed'));
$form->addMultilineText('subtitle', 'Slogan', 'Displayed below the title in the site header', 'Regain vacant space now!', array('(.|\n)*', 0, 200, 'Unknown error'));
$form->addMultilineText('description', 'Description', 'Only visible for search engines<br>Describe your site concisely', 'Using cubic melons stacking and transport will be more efficient and economical', array('.*', 0, 80, 'Unknown error'));
$form->addArray('keywords', 'Keywords', 'Only visible for search engines<br>Enter keywords defining your site', array('cubic', 'melons', 'efficient', 'stacking'), array('.*', 0, 80, 'Unknown error'));

$form->addSeparator();

$form->setResponse('Saved', 'Not saved');

if ($form->submitted())
{
	if ($form->validate())
	{
		Db::exec("UPDATE setting SET value = '" . Db::escape($form->get('title')) . "' WHERE key = 'title';");
		Db::exec("UPDATE setting SET value = '" . Db::escape($form->get('subtitle')) . "' WHERE key = 'subtitle';");
		Db::exec("UPDATE setting SET value = '" . Db::escape($form->get('description')) . "' WHERE key = 'description';");
		Db::exec("UPDATE setting SET value = '" . Db::escape($form->get('keywords')) . "' WHERE key = 'keywords';");
	}
	$form->finish();
}

$settings = Db::query("SELECT * FROM setting;");
while ($setting = $settings->fetch())
	$form->set($setting['key'], $setting['value']);

Hooks::emit('admin-header');

Core::set('settings', $form);
Core::render('admin/settings.tpl');

Hooks::emit('admin-footer');
exit;

?>
