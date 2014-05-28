<?php

$form = new Form('settings');

$form->addSection(__('Settings'), '');

$contact = array();
$table = Db::query("SELECT * FROM module_contact;");
while ($row = $table->fetch())
	$contact[$row['key']] = $row['value'];

$directories = array();
$assets = new RecursiveDirectoryIterator('assets/');
foreach (new RecursiveIteratorIterator($assets) as $directory_name => $info)
{
	if ($info->isDir() && !$info->isDot())
	{
		$directories[$directory_name] = $directory_name;
	}
}
$form->addDropdown('directory', __('Directory'), '', $directories);

$form->addSeparator();
$form->setResponse(__('Saved'), __('Not saved'));

if ($form->submitted())
{
	if ($form->validate())
		/*foreach ($contact as $key => $value)
			Db::exec("
			UPDATE module_contact SET
				value = '" . Db::escape($form->get($key)) . "'
			WHERE key = '" . Db::escape($key) . "';");*/
	$form->finish();
}

Hooks::emit('admin-header');

Module::set('settings', $form);
Template::render('admin/index.tpl');

Hooks::emit('admin-footer');
exit;