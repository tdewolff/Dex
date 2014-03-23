<?php

$form = new Form('login');

$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username or Email', '', '', array('[a-zA-Z0-9-_]*|[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}', 3, 50, 'Must be a valid username or email address format'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();

$form->setSubmit('<i class="fa fa-sign-in"></i>&ensp;Login');
$form->setResponse('', 'Not logged in');

if ($form->submitted())
{
	if ($form->validate())
	{
		if (User::isBlocked($form->get('username')))
			$form->appendError('Too many login attempts within short time, please wait 15 minutes');
		else
		{
			$user = Db::singleQuery("SELECT * FROM user WHERE username = '" . Db::escape($form->get('username')) . "' LIMIT 1;");
			if (!$user)
				$user = Db::singleQuery("SELECT * FROM user WHERE email = '" . Db::escape($form->get('username')) . "' LIMIT 1;");

			if ($user && Bcrypt::verify($form->get('password'), $user['password']))
			{
				User::logIn($user['user_id']);

				Log::notice(print_r($_GET, true));

				if (isset($_GET['url']))
					$form->setRedirect('/' . Common::$base_url . $_GET['url']);
				else if (Common::$request_url == 'admin/login/' || Common::$request_url == 'admin/logout/')
					$form->setRedirect('/' . Common::$base_url . 'admin/');
				else
					$form->setRedirect('/' . Common::$base_url . Common::$request_url);
			}
			else
			{
				User::addAttempt($form->get('username'));
				$form->appendError('Username and password combination is incorrect');
			}
		}
	}
	$form->finish();
}

Core::addTitle('Admin panel');

Hooks::emit('admin-header');

Core::assign('login', $form);
Core::render('admin/login.tpl');

Hooks::emit('admin-footer');
exit;

?>
