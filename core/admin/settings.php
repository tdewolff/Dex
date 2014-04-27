<?php

$form = new Form('settings');

$form->addSection(_('Settings'), _('General site settings'));
$form->addText('title', _('Title'), _('Displayed in the titlebar and site header'), '', array('.*', 1, 30, _('Unknown error')));
$form->addMultilineText('subtitle', _('Slogan'), _('Displayed below the title in the site header'), '', array('(.|\n)*', 0, 200, _('Unknown error')));
$form->addMultilineText('description', _('Description'), _('Only visible for search engines<br>Describe your site concisely'), '', array('.*', 0, 80, _('Unknown error')));
$form->addArray('keywords', _('Keywords'), _('Only visible for search engines<br>Enter keywords defining your site'), array(), array('.*', 0, 80, _('Unknown error')));
$form->addSeparator();

$form->addDropdown('language', _('Language'), '', Language::getAll());
$form->addSeparator();

$form->setResponse(_('Saved'), _('Not saved'));

if ($form->submitted())
{
	if ($form->validate())
	{
		Db::exec("BEGIN;
			UPDATE setting SET value = '" . Db::escape($form->get('title')) . "' WHERE key = 'title';
			UPDATE setting SET value = '" . Db::escape($form->get('subtitle')) . "' WHERE key = 'subtitle';
			UPDATE setting SET value = '" . Db::escape($form->get('description')) . "' WHERE key = 'description';
			UPDATE setting SET value = '" . Db::escape($form->get('keywords')) . "' WHERE key = 'keywords';
			UPDATE setting SET value = '" . Db::escape($form->get('language')) . "' WHERE key = 'language';
		COMMIT;");
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
