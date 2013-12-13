<?php

$form = new Form('setup');

$form->addSection('Settings', 'General site settings');
$form->addText('title', 'Title', 'Displayed in the titlebar and site header', 'CubicMelonFirm', array('[a-zA-Z0-9\s]*', 1, 25, 'Only alphanumeric characters and spaces allowed'));
$form->addMultilineText('subtitle', 'Slogan', 'Displayed below the title in the site header', 'Regain vacant space now!', array('(.|\n)*', 0, 200, 'Unknown error'));
$form->addMultilineText('description', 'Description', 'Only visible for search engines<br>Describe your site concisely', 'Using cubic melons stacking and transport will be more efficient and economical', array('.*', 0, 80, 'Unknown error'));
$form->addArray('keywords', 'Keywords', 'Only visible for search engines<br>Enter keywords defining your site', array('cubic', 'melons', 'efficient', 'stacking'), array('.*', 0, 80, 'Unknown error'));

$form->addSection('Admin account', 'Admin account gives full access to the admin panel, meant for site owners.');
$form->addText('username', 'Username', '', 'admin', array('[a-zA-Z0-9-_]*', 3, 16, 'Only alphanumeric and (-_) characters allowed'));
$form->addEmail('email', 'Email address', 'Used for notifications and password recovery');
$form->addPassword('password', 'Password', '');
$form->addPasswordConfirm('password2', 'password', 'Confirm password', '');

$form->addSeparator();

$form->setSubmit('<i class="fa fa-asterisk"></i>&ensp;Setup');
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
            email TEXT,
            password TEXT,
            permission TEXT
        );

        DROP TABLE IF EXISTS recover;
        CREATE TABLE recover (
            recover_id INTEGER PRIMARY KEY,
            user_id INTEGER,
            token TEXT,
            expiry_time INTEGER
        );

        DROP TABLE IF EXISTS link;
        CREATE TABLE link (
            link_id INTEGER PRIMARY KEY,
            url TEXT,
            title TEXT,
            template_name TEXT,
            modify_time INTEGER
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

        INSERT INTO link (url, title, template_name, modify_time) VALUES (
            '',
            'Home',
            'static',
            '" . $db->escape(time()) . "'
        );

        INSERT INTO content (link_id, name, content) VALUES (
            '1',
            'content',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean et mattis nulla, imperdiet ornare justo. Aliquam ultrices elit in sem viverra tristique. Nam consectetur scelerisque dolor, sit amet varius erat pretium blandit. Fusce at urna nisi. Mauris vel lorem in ipsum eleifend iaculis. Donec dictum laoreet sem. Donec euismod magna vel lorem rhoncus bibendum. Nunc at tincidunt lorem. Suspendisse congue metus pharetra ultrices vehicula. Vestibulum congue luctus ipsum sit amet vulputate. Nam venenatis dictum risus, vel viverra quam. Sed convallis, magna ut varius pellentesque, velit augue auctor tortor, iaculis pellentesque nisi mauris dapibus nulla. Nam vel enim at velit facilisis laoreet. Aliquam blandit lobortis neque, et scelerisque risus imperdiet vel. Nulla enim diam, semper sed dolor nec, gravida congue arcu. Proin varius est a dui varius, eget posuere nulla aliquam.

Sed quis enim sit amet eros fermentum aliquam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque malesuada est vitae feugiat sodales. Curabitur vehicula ullamcorper mauris. Cras eu mauris nisi. Donec in eros dui. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec aliquam neque sed libero porta mollis. Maecenas vitae varius erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In imperdiet quam nec magna vehicula rhoncus. Phasellus semper malesuada nunc, et porta purus viverra malesuada. Proin nec tempor ante. Nunc aliquam augue nec est vehicula euismod.

Aliquam erat volutpat. Ut semper dolor magna, non faucibus elit fermentum ut. Sed mattis mauris non dolor lacinia gravida non eget lectus. Etiam malesuada, lacus in luctus ullamcorper, purus lectus vehicula dolor, sit amet ullamcorper augue odio vel ligula. Morbi sodales lacus ac dignissim interdum. Quisque nec nisi blandit, aliquam diam vitae, porttitor neque. Quisque vel eros eu enim mollis sodales nec sit amet lectus. Praesent scelerisque nisl at pharetra blandit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse potenti. Suspendisse consequat eleifend turpis, nec molestie risus varius et. Duis iaculis neque enim, sit amet lacinia diam suscipit quis. Nullam feugiat, risus id volutpat tempus, tellus justo aliquet metus, id lobortis lectus est id leo. Mauris vestibulum odio in dui lobortis, a pharetra massa vulputate. Nulla sed posuere augue.

Phasellus sit amet consectetur nunc. Quisque lacinia accumsan tellus, nec cursus lectus. Phasellus eros massa, vulputate id purus sit amet, dignissim imperdiet quam. Aenean vitae fringilla purus, pulvinar congue magna. Cras vel dolor justo. Mauris scelerisque placerat justo, vel interdum elit mattis quis. Maecenas porta, leo non vestibulum auctor, sem enim viverra erat, in luctus nisl nunc eleifend ipsum. Fusce diam nibh, rutrum quis rhoncus id, egestas mattis eros.

Nunc vehicula risus sem, id suscipit metus luctus quis. Cras egestas libero vehicula, varius nibh pellentesque, sagittis nisl. Fusce ut erat non mauris pulvinar congue id nec libero. Pellentesque vel vulputate nunc. Maecenas adipiscing scelerisque placerat. Praesent lobortis sem lorem, commodo gravida orci ullamcorper cursus. Etiam id consequat libero, euismod dapibus libero. Suspendisse ligula lectus, rhoncus et erat vitae, lacinia rutrum nisi.'
        );

        INSERT INTO setting (key, value) VALUES (
            'title',
            '" . $db->escape($form->get('title')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'subtitle',
            '" . $db->escape($form->get('subtitle')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'description',
            '" . $db->escape($form->get('description')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'keywords',
            '" . $db->escape($form->get('keywords')) . "'
        );

        INSERT INTO setting (key, value) VALUES (
            'theme',
            'default'
        );

        INSERT INTO user (username, email, password, permission) VALUES (
            '" . $db->escape($form->get('username')) . "',
            '" . $db->escape($form->get('email')) . "',
            '" . $db->escape(Bcrypt::hash($form->get('password'))) . "',
            'admin'
        );");

        Session::logIn($db->lastId(), 'admin');
        $form->setRedirect('/' . $base_url . 'admin/');
    }
    $form->finish();
}

Core::addTitle('Setup Dexterous');

Hooks::emit('admin-header');

Core::assign('setup', $form);
Core::render('admin/setup.tpl');

Hooks::emit('admin-footer');
exit;

?>
