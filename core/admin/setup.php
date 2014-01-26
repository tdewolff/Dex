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
$form->setResponse('', 'Not setup');

if ($form->submitted())
{
	if ($form->validate())
	{
		$valid = Db::exec("
		CREATE TABLE setting (
			setting_id INTEGER PRIMARY KEY,
			key TEXT,
			value TEXT
		);

		CREATE TABLE user (
			user_id INTEGER PRIMARY KEY,
			username TEXT,
			email TEXT,
			password TEXT,
			role TEXT
		);

		CREATE TABLE recover (
			recover_id INTEGER PRIMARY KEY,
			user_id INTEGER,
			token TEXT,
			expiry_time INTEGER
		);

		CREATE TABLE bruteforce (
			bruteforce_id INTEGER PRIMARY KEY,
			n INTEGER,
			time INTEGER,
			ip_address TEXT,
			username TEXT
		);

		CREATE TABLE link (
			link_id INTEGER PRIMARY KEY,
			url TEXT,
			title TEXT,
			template_name TEXT,
			modify_time INTEGER
		);

		CREATE TABLE content (
			content_id INTEGER PRIMARY KEY,
			link_id INTEGER,
			name TEXT,
			content TEXT,
			modify_time INTEGER,
			FOREIGN KEY(link_id) REFERENCES link(link_id)
		);

		CREATE TABLE module (
			module_id INTEGER PRIMARY KEY,
			module_name TEXT,
			enabled INTEGER
		);

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
			'" . Db::escape(time()) . "'
		);

		INSERT INTO content (link_id, name, content, modify_time) VALUES (
			'1',
			'content',
			'" . Db::escape('<h3>Sample content</h3>
			 <p>This is a sample page to get you going!</p>
			 <p>When logged in you can click on \'Edit\' above and start typing right away. Select this piece of text for example and start styling with <b>bold</b> and <i>italic</i>.</p>
			 <ul><li>Create a bulleted list by typing \'-\' or \'*\' and hitting enter</li></ul>
			 <ol><li>Or list things by starting the line with \'1. \'</li><li>etc.</li></ol>
			 <hr>
			 <p>Two enters creates a divider and you can quote someone:</p>
			 <blockquote>In 1972 a crack commando unit was sent to prison by a military court for a crime they didn\'t commit. These men promptly escaped from a maximum security stockade to the Los Angeles underground. Today, still wanted by the government, they survive as soldiers of fortune. If you have a problem, if no one else can help, and if you can find them, maybe you can hire the A-Team.</blockquote>
			') . "',
			'" . Db::escape(time()) . "'
		);

		INSERT INTO setting (key, value) VALUES (
			'title',
			'" . Db::escape($form->get('title')) . "'
		);

		INSERT INTO setting (key, value) VALUES (
			'subtitle',
			'" . Db::escape($form->get('subtitle')) . "'
		);

		INSERT INTO setting (key, value) VALUES (
			'description',
			'" . Db::escape($form->get('description')) . "'
		);

		INSERT INTO setting (key, value) VALUES (
			'keywords',
			'" . Db::escape($form->get('keywords')) . "'
		);

		INSERT INTO setting (key, value) VALUES (
			'theme',
			'plain'
		);

		INSERT INTO user (username, email, password, role) VALUES (
			'" . Db::escape($form->get('username')) . "',
			'" . Db::escape($form->get('email')) . "',
			'" . Db::escape(Bcrypt::hash($form->get('password'))) . "',
			'admin'
		);

		CREATE TABLE stats (
			stat_id INTEGER PRIMARY KEY,
			n INTEGER,
			time INTEGER,
			ip_address TEXT,
			request_url TEXT,
			referral TEXT
		);");

		if (!$valid)
		{
			Db::unlink();
			user_error('Could not setup site, database error', ERROR);
		}

		User::logIn(Db::lastId());
		$form->setRedirect('/' . Common::$base_url . 'admin/');
	}
	$form->finish();
}

Core::addTitle('Setup Dex');

Hooks::emit('admin-header');

Core::assign('setup', $form);
Core::render('admin/setup.tpl');

Hooks::emit('admin-footer');
exit;

?>
