<?php

$form = new Form('setup');

$form->addSection('Settings', 'General site settings');
$form->addText('title', 'Title', 'Displayed in the titlebar and site header', 'CubicMelonFirm', array('[a-zA-Z0-9\s]*', 1, 25, 'Only alphanumeric characters and spaces allowed'));
$form->addMultilineText('subtitle', 'Slogan', 'Displayed below the title in the site header', 'Regain vacant space now!', array('(.|\n)*', 0, 200, 'Unknown error'));
$form->addMultilineText('description', 'Description', 'Only visible for search engines<br>Describe your site concisely', 'Using cubic melons stacking and transport will be more efficient and economical', array('.*', 0, 80, 'Unknown error'));
$form->addArray('keywords', 'Keywords', 'Only visible for search engines<br>Enter keywords defining your site', array('cubic', 'melons', 'efficient', 'stacking'), array('.*', 0, 80, 'Unknown error'));

$form->addSection('Admin account', 'Admin account gives full access to the admin panel, meant for site owners.');
$form->addText('username', 'Username', '', 'admin', array('[a-zA-Z0-9-_]*', 3, 16, 'Only alphanumeric and (-_) characters allowed'));
$form->addPassword('password', 'Password', '');
$form->addPasswordConfirm('password2', 'password', 'Confirm password', '');

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

        INSERT INTO content (link_id, name, content, modify_time) VALUES (
            '1',
            'content',
            'This is a sample home page containing lots of fake latin words.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean et mattis nulla, imperdiet ornare justo. Aliquam ultrices elit in sem viverra tristique. Nam consectetur scelerisque dolor, sit amet varius erat pretium blandit. Fusce at urna nisi. Mauris vel lorem in ipsum eleifend iaculis. Donec dictum laoreet sem. Donec euismod magna vel lorem rhoncus bibendum. Nunc at tincidunt lorem. Suspendisse congue metus pharetra ultrices vehicula. Vestibulum congue luctus ipsum sit amet vulputate. Nam venenatis dictum risus, vel viverra quam. Sed convallis, magna ut varius pellentesque, velit augue auctor tortor, iaculis pellentesque nisi mauris dapibus nulla. Nam vel enim at velit facilisis laoreet. Aliquam blandit lobortis neque, et scelerisque risus imperdiet vel. Nulla enim diam, semper sed dolor nec, gravida congue arcu. Proin varius est a dui varius, eget posuere nulla aliquam.

Sed quis enim sit amet eros fermentum aliquam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque malesuada est vitae feugiat sodales. Curabitur vehicula ullamcorper mauris. Cras eu mauris nisi. Donec in eros dui. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec aliquam neque sed libero porta mollis. Maecenas vitae varius erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In imperdiet quam nec magna vehicula rhoncus. Phasellus semper malesuada nunc, et porta purus viverra malesuada. Proin nec tempor ante. Nunc aliquam augue nec est vehicula euismod.'

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

        INSERT INTO user (username, password, permission) VALUES (
            '" . $db->escape($form->get('username')) . "',
            '" . $db->escape($bcrypt->hash($form->get('password'))) . "',
            'admin'
        );");

        $form->clearSession();
        Session::logIn($db->last_id(), 'admin');

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
