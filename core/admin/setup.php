<?php
$form = new Form('setup');

$form->addSection(__('Settings'), __('General site settings'));
$form->addText('title', __('Title'), __('Displayed in the titlebar and site header'), '', array('.*', 1, 30, __('Unknown error')));
$form->addMultilineText('subtitle', __('Slogan'), __('Displayed below the title in the site header'), '', array('(.|\n)*', 0, 200, __('Unknown error')));
$form->addMultilineText('description', __('Description'), __('Only visible for search engines<br>Describe your site concisely'), '', array('.*', 0, 80, __('Unknown error')));
$form->addArray('keywords', __('Keywords'), __('Only visible for search engines<br>Enter keywords defining your site'), array(), array('.*', 0, 80, __('Unknown error')));

$form->addSeparator();

$form->addDropdown('language', __('Language'), '', Language::getAll());

$form->addSection(__('Admin account'), __('Admin account gives full access to the admin panel, meant for site owners.'));
$form->addText('username', __('Username'), '', 'admin', array('[a-zA-Z0-9-_]*', 3, 16, __('Only alphanumeric and (-_) characters allowed')));
$form->addEmail('email', __('Email address'), __('Used for notifications and password recovery'));
$form->addPassword('password', __('Password'), '');
$form->addPasswordConfirm('password2', 'password', __('Confirm password'), '');

$form->addSeparator();

$form->setSubmit('<i class="fa fa-asterisk"></i>&ensp;' . __('Set up'));
$form->setResponse('', __('Not set up'));

if ($form->submitted())
{
	if ($form->validate())
	{
		Language::load($form->get('language'));

		$valid = Db::exec("BEGIN;
			CREATE TABLE setting (
				setting_id INTEGER PRIMARY KEY,
				key TEXT UNIQUE,
				value TEXT
			);

			CREATE TABLE user (
				user_id INTEGER PRIMARY KEY,
				username TEXT UNIQUE,
				email TEXT UNIQUE,
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

			CREATE TABLE stats (
				stat_id INTEGER PRIMARY KEY,
				n INTEGER,
				time INTEGER,
				end_time INTEGER,
				ip_address TEXT,
				request_url TEXT,
				referral TEXT
			);

			CREATE TABLE link (
				link_id INTEGER PRIMARY KEY,
				url TEXT UNIQUE,
				title TEXT,
				template_name TEXT
			);

			CREATE TABLE content (
				content_id INTEGER PRIMARY KEY,
				link_id INTEGER,
				user_id INTEGER,
				name TEXT,
				content TEXT,
				modify_time INTEGER,
				FOREIGN KEY(user_id) REFERENCES user(user_id)
			);

			CREATE TABLE module (
				module_id INTEGER PRIMARY KEY,
				module_name TEXT UNIQUE,
				enabled INTEGER
			);

			CREATE TABLE link_module (
				link_module_id INTEGER PRIMARY KEY,
				link_id INTEGER,
				module_name TEXT,
				FOREIGN KEY(link_id) REFERENCES link(link_id),
				FOREIGN KEY(module_name) REFERENCES module(module_name)
			);

			INSERT INTO link (url, title, template_name) VALUES (
				'',
				'" . Db::escape(__('Home')) . "',
				'static'
			);

			INSERT INTO content (link_id, user_id, name, content, modify_time) VALUES (
				'1',
				'1',
				'content',
				'" . Db::escape('<h3>' . __('Sample content') . '</h3>
				 <p>' . __('When logged in you can start editing by clicking this text. Try it!') . '</p>
				 <p>' . __('Select text to make it <b>bold</b> or <i>italic</i>, or to insert links or quotes like below') . ':</p>
				 <blockquote>' . __('In 1972 a crack commando unit was sent to prison by a military court for a crime they didn&#x2019;t commit. These men promptly escaped from a maximum security stockade to the Los Angeles underground. Today, still wanted by the government, they survive as soldiers of fortune. If you have a problem, if no one else can help, and if you can find them, maybe you can hire the A-Team.') . '</blockquote>
				 <hr contenteditable="false">
				 <p>' . __('Two enters create a divider and you can create lists too') . ':</p>
				 <ul>' . __('<li>Typing &#x2018;- &#x2019; creates a list</li><li>And so does &#x2018;* &#x2019; too</li>') . '</ul>
				 <ol>' . __('<li>An ordered list is created by typing &#x2018;1. &#x2019;</li><li>And so forth&#x2026;</li>') . '</ol>
				') . "',
				'" . Db::escape(time()) . "'
			);

			INSERT INTO setting (key, value) VALUES (
				'language',
				'" . Db::escape($form->get('language')) . "'
			);

			INSERT INTO setting (key, value) VALUES (
				'theme',
				'default'
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

			INSERT INTO user (username, email, password, role) VALUES (
				'" . Db::escape($form->get('username')) . "',
				'" . Db::escape($form->get('email')) . "',
				'" . Db::escape(Bcrypt::hash($form->get('password'))) . "',
				'admin'
			);
		COMMIT;");

		if (!$valid)
		{
			Db::unlink();
			user_error('Could not setup site, database error', ERROR);
		}

		User::logIn($form->get('username'), $form->get('password'));
		$form->setRedirect('/' . Common::$base_url . 'admin/');
	}
	$form->finish();
}

Core::addTitle(__('Dex setup'));

Hooks::emit('admin-header');

Core::set('setup', $form);
Core::render('admin/setup.tpl');

Hooks::emit('admin-footer');
exit;
