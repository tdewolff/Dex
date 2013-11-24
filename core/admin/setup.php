<?php

$form = new Form('setup');

$form->addSection('Settings', 'General site settings');
$form->addText('site_title', 'Site title', 'Displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 25, 'May contain alphanumeric characters and spaces'));
$form->addMultilineText('site_subtitle', 'Site subtitle', 'Displayed in the header', '', array('(.|\n)*', 0, 200, 'Unknown error'));
$form->addText('site_description', 'Site description', 'Describe the site concisely', '', array('.*', 0, 80, 'Unknown error'));
$form->addArray('site_keywords', 'Site keywords', '', array('.*', 0, 80, 'Unknown error'));

$form->addSection('Admin account', 'Admin account gives full access to the admin panel, meant for site owners.');
$form->addText('admin_username', 'Admin username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
$form->addPassword('admin_password', 'Admin password', '');
$form->addPasswordConfirm('admin_password2', 'admin_password', 'Admin password', 'Confirm');

$form->addSection('User account', 'User account gives restricted access to the admin panel, meant for clients. Leave empty to skip.');
$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
$form->addPassword('password', 'Password', '');
$form->addPasswordConfirm('password2', 'password', 'Password', 'Confirm');
$form->optionalTogether(array('username', 'password', 'password2'));

$form->addSeparator();

$form->setSubmit('<i class="icon-asterisk"></i>&ensp;Setup');
$form->setResponse('', '(not setup)');

if ($form->submitted())
{
    if ($form->validate())
    {
        $db->exec("
        DROP TABLE IF EXISTS setting;
        CREATE TABLE setting (
            setting_id INTEGER PRIMARY KEY,
            key TEXT,
            value TEXT
        );

        DROP TABLE IF EXISTS user;
        CREATE TABLE user (
            user_id INTEGER PRIMARY KEY,
            username TEXT,
            password TEXT,
            permission TEXT
        );

        DROP TABLE IF EXISTS link;
        CREATE TABLE link (
            link_id INTEGER PRIMARY KEY,
            url TEXT,
            title TEXT,
            template_name TEXT
        );

        DROP TABLE IF EXISTS content;
        CREATE TABLE content (
            content_id INTEGER PRIMARY KEY,
            link_id INTEGER,
            name TEXT,
            content TEXT,
            FOREIGN KEY(link_id) REFERENCES link(link_id)
        );

        DROP TABLE IF EXISTS module;
        CREATE TABLE module (
            module_id INTEGER PRIMARY KEY,
            module_name TEXT,
            enabled INTEGER
        );

        DROP TABLE IF EXISTS link_module;
        CREATE TABLE link_module (
            link_module_id INTEGER PRIMARY KEY,
            link_id INTEGER,
            module_name TEXT,
            FOREIGN KEY(link_id) REFERENCES link(link_id),
            FOREIGN KEY(module_name) REFERENCES module(module_name)
        );

        INSERT INTO setting (key, value) VALUES (
            'title',
            '" . $db->escape($form->get('site_title')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'subtitle',
            '" . $db->escape($form->get('site_subtitle')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'description',
            '" . $db->escape($form->get('site_description')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'keywords',
            '" . $db->escape($form->get('site_keywords')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'theme',
            'default'
        );

        INSERT INTO user (username, password, permission) VALUES (
            '" . $db->escape($form->get('admin_username')) . "',
            '" . $db->escape($bcrypt->hash($form->get('admin_password'))) . "',
            'admin'
        );");
        $user_id = $db->last_id();

        $username = $form->get('username');
        if (strlen($username))
        {
            $db->exec("
            INSERT INTO user (username, password, permission) VALUES (
                '" . $db->escape($form->get('username')) . "',
                '" . $db->escape($bcrypt->hash($form->get('password'))) . "',
                'user'
            );");
        }

        $form->clearSession();
        Session::logIn($user_id, 'admin');

        $form->setRedirect('/' . $base_url . 'admin/');
    }
    $form->finish();
}

Core::addTitle('Setup Dexterous');
Core::addStyle('include/normalize.min.css');
Core::addStyle('vendor/font-awesome.min.css');
Core::addStyle('vendor/fancybox.min.css');
Core::addStyle('admin.min.css');
Core::addScript('vendor/jquery-2.0.3.min.js');
Core::addScript('vendor/doT.min.js');
Core::addScript('include/slidein.min.js');
Core::addScript('include/api.min.js');
Core::addDeferredScript('vendor/jquery.fancybox.min.js');
Core::addDeferredScript('admin.min.js');

Hooks::emit('admin-header');

Core::assign('setup', $form);
Core::render('admin/setup.tpl');

Hooks::emit('admin-footer');
exit;

?>
