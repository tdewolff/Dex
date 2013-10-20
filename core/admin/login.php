<?php

$form = new Form('login');

$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9\-_\.]*', 3, 20, 'May contain alphanumeric characters and (-_.)'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();
$form->addSubmit('login', '<i class="icon-signin"></i>&ensp;Login');

if ($form->submittedBy('login'))
	if ($form->verifyPost())
	{
		$account = $db->querySingle('SELECT * FROM account WHERE username = "' . $db->escape($form->get('username')) . '" LIMIT 1;');
		if ($account && $bcrypt->verify($form->get('password'), $account['password']))
		{
			Session::logIn($account['account_id'], $account['permission']);
			$form->unsetSession();
		}
		else
			$form->appendError('Login username and password combination is incorrect');
	}
	else
		$form->postToSession();

if (!Session::isUser())
{
	Core::addTitle('Admin panel');

	Hooks::emit('admin_header');

	$form->sessionToForm();
	if ($request_url == 'admin/logout/')
		$form->setAction('/' . $base_url . 'admin/');

	Core::assign('login', $form);
	Core::render('admin/login.tpl');

	Hooks::emit('admin_footer');
	exit;
}

?>
