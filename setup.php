<?php

$form = new Form('setup', 'Setting up Dexterous', 'Your site has not been setup yet. Fill out the forms below, any value can later on be change in the admin panel.');
$form->addSection('Settings', 'General site settings');
$form->addText('site_title', 'Site title', 'Displayed in the titlebar', array('[a-zA-Z0-9\s]*', 1, 25, 'May contain alphanumeric characters and spaces'));
$form->addText('site_subtitle', 'Site subtitle', 'Displayed in the header', array('[a-zA-Z0-9\s<>\?!]*', 1, 200, 'May contain alphanumeric characters and spaces'));
$form->addText('site_description', 'Site description', 'Describe the site concisely', array('[a-zA-Z0-9\s,\.\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));
$form->addText('site_keywords', 'Site keywords', 'Comma-separate tags', array('[a-zA-Z0-9\s,\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));

$form->addSection('Admin account', 'Admin account gives full access to the admin panel, meant for site owners.');
$form->addText('admin_username', 'Admin username', '', array('[a-zA-Z0-9\-_\.]*', 3, 20, 'May contain alphanumeric characters and (-_.)'));
$form->addPassword('admin_password', 'Admin password', '');
$form->addPasswordConfirm('admin_password2', 'admin_password', 'Admin password', 'Confirm');

$form->addSection('User account', 'User account gives restricted access to the admin panel, meant for clients. Leave empty to skip.');
$form->addText('username', 'Username', '', array('[a-zA-Z0-9\-_\.]*', 3, 20, 'May contain alphanumeric characters and (-_.)'));
$form->addPassword('password', 'Password', '');
$form->addPasswordConfirm('password2', 'password', 'Password', 'Confirm');
$form->allowEmptyTogether(array('username', 'password', 'password2'));

$form->addSeparator();
$form->addSubmit('setup', '<i class="icon-asterisk"></i>&ensp;Setup');

$isSetup = false;
if ($form->submittedBy('setup'))
{
	if ($form->verifyPost())
	{
		$db->exec("
		DROP TABLE IF EXISTS settings;
		CREATE TABLE settings (
			id INTEGER PRIMARY KEY,
			key VARCHAR(20),
			value VARCHAR(200)
		);

		DROP TABLE IF EXISTS accounts;
		CREATE TABLE accounts (
			id INTEGER PRIMARY KEY,
			username VARCHAR(20),
			password VARCHAR(60),
			userlevel INTEGER(1)
		);

		DROP TABLE IF EXISTS modules;
		CREATE TABLE modules (
			id INTEGER PRIMARY KEY,
			name VARCHAR(50),
			enabled INT(1)
		);

		DROP TABLE IF EXISTS links;
		CREATE TABLE links (
			id INTEGER PRIMARY KEY,
			link VARCHAR(50),
			title VARCHAR(50)
		);

		DROP TABLE IF EXISTS link_modules;
		CREATE TABLE link_modules (
			id INTEGER PRIMARY KEY,
			link_id INTEGER,
			module_name VARCHAR(50)
		);

		INSERT INTO settings (key, value) VALUES (
			'title',
			'" . $db->escape($form->get('site_title')) . "'
		);

		INSERT INTO settings (key, value) VALUES (
			'subtitle',
			'" . $db->escape($form->get('site_subtitle')) . "'
		);

		INSERT INTO settings (key, value) VALUES (
			'description',
			'" . $db->escape($form->get('site_description')) . "'
		);

		INSERT INTO settings (key, value) VALUES (
			'keywords',
			'" . $db->escape($form->get('site_keywords')) . "'
		);

		INSERT INTO settings (key, value) VALUES (
			'theme',
			'default'
		);

		INSERT INTO accounts (username, password, userlevel) VALUES (
			'" . $db->escape($form->get('admin_username')) . "',
			'" . $db->escape($bcrypt->hash($form->get('admin_password'))) . "',
			'0'
		);");

		$username = $form->get('username');
		if (strlen($username))
		{
			$db->exec("
			INSERT INTO accounts (username, password, userlevel) VALUES (
				'" . $db->escape($form->get('username')) . "',
				'" . $db->escape($bcrypt->hash($form->get('password'))) . "',
				'1'
			);");
		}

		$form->unsetSession();
		$isSetup = true;
	}
	else
		$form->postToSession();
}

if (!$isSetup)
{
	Dexterous::addTitle('Setup Dexterous');
	Dexterous::addStyle('resources/styles/admin.css');
	Dexterous::addStyle('resources/styles/font-awesome.css');
	Dexterous::addDeferredScript('resources/scripts/jquery.js');
	Dexterous::addDeferredScript('resources/scripts/admin.js');

	Hooks::emit('header');

	$form->sessionToForm();
	Dexterous::assign('setup', $form);

	Dexterous::render('setup.tpl');

	Hooks::emit('footer');
	exit;
}

?>
