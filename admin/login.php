<?php

$form = new Form('login', 'Login to admin panel');
$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username', '', array('[a-zA-Z0-9\-_\.]*', 3, 20, 'May contain alphanumeric characters and (-_.)'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();
$form->addSubmit('login', '<i class="icon-signin"></i>&ensp;Login');

if ($form->submittedBy('login'))
{
	if ($form->verifyPost())
	{
		$account = $db->querySingle('SELECT * FROM accounts WHERE username = "' . $db->escape($form->get('username')) . '" LIMIT 1;');
		if ($account && $bcrypt->verify($form->get('password'), $account['password']))
		{
			Session::logIn($account['id'], $account['userlevel']);
			$form->unsetSession();
		}
		else
		{
			$form->appendError('Login username and password combination is incorrect');
		}
	}
	else
		$form->postToSession();
}

if (!Session::isUser())
{
	Dexterous::addTitle('Admin panel');

	Hooks::emit('header');

	$form->sessionToForm();
	$form->setupForm($smarty);

	if ($request_uri == 'admin/logout/')
		Dexterous::assign('form_action', $base_url . 'admin/');

	Dexterous::render('admin/login.tpl');

	Hooks::emit('footer');
	exit;
}

?>
