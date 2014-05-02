<?php

if ($dex_conf->get('ssl') &&  $_SERVER['HTTPS'] != 'on')
{
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

$form = new Form('login');

$form->addSection(__('Log in'), __('You must log in before you can continue to the admin panel.'));
$form->addText('username', __('Username'), '', '', array('[a-zA-Z0-9-_]*', 3, 16, __('Must be a valid username')));
$form->addPassword('password', __('Password'), '');

$form->addSeparator();

$form->setSubmit('<i class="fa fa-sign-in"></i>&ensp;' . __('Log in'));
$form->setResponse('', __('Not logged in'));

if ($form->submitted())
{
	if ($form->validate())
	{
		if (User::isBlocked($form->get('username')))
			$form->appendError(__('Too many login attempts within short time, please wait 15 minutes'));
		else
		{
			$user = Db::singleQuery("SELECT user_id, password FROM user WHERE username = '" . Db::escape($form->get('username')) . "' LIMIT 1;");
			if ($user && Bcrypt::verify($form->get('password'), $user['password']))
			{
				User::logIn($user['user_id']);

				if (isset($url[1]) && strpos($url[1], 'r=') === 0)
					$form->setRedirect('/' . Common::$base_url . rawurldecode(substr($url[1], 2)));
				else if (Common::$request_url == 'admin/logout/')
					$form->setRedirect('/' . Common::$base_url . 'admin/');
				else
					$form->setRedirect('/' . Common::$base_url . Common::$request_url);
			}
			else
			{
				User::addAttempt($form->get('username'));
				$form->appendError(__('Username and password combination is incorrect'));
			}
		}
	}
	$form->finish();
}

Core::addTitle('Admin panel');

Hooks::emit('admin-header');

Core::set('login', $form);
Core::render('admin/login.tpl');

Hooks::emit('admin-footer');
exit;
