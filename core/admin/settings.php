<?php

$form = new Form('settings');

$form->addSection(__('Settings'), __('General site settings'));
$form->addText('title', __('Title'), __('Displayed in the titlebar and site header'), '', array('.*', 1, 30, __('Unknown error')));
$form->addMultilineText('subtitle', __('Slogan'), __('Displayed below the title in the site header'), '', array('(.|\n)*', 0, 200, __('Unknown error')));
$form->addMultilineText('description', __('Description'), __('Only visible for search engines<br>Describe your site concisely'), '', array('.*', 0, 80, __('Unknown error')));
$form->addArray('keywords', __('Keywords'), __('Only visible for search engines<br>Enter keywords defining your site'), array(), array('.*', 0, 80, __('Unknown error')));
$form->addSeparator();

$form->addDropdown('language', __('Language'), '', Language::getAll());
$form->addSeparator();

$form->setResponse(__('Saved'), __('Not saved'));

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

foreach ($settings as $key => $value)
	$form->set($key, $value);

Hooks::emit('admin-header');

Core::set('settings', $form);
Core::render('admin/settings.tpl');

Hooks::emit('admin-footer');
exit;
