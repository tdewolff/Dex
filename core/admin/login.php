<?php

$form = new Form('login');

$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'Only alphanumeric and (-_) characters allowed'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();

$form->setSubmit('<i class="fa fa-sign-in"></i>&ensp;Login');
$form->setResponse('', '(not logged in)');

if ($form->submitted())
{
	if ($form->validate())
	{
		$user = $db->querySingle("SELECT * FROM user WHERE username = '" . $db->escape($form->get('username')) . "' LIMIT 1;");
		if ($user && Bcrypt::verify($form->get('password'), $user['password']))
		{
			Session::logIn($user['user_id'], $user['permission']);
			$form->setRedirect('/' . $base_url . $request_url);
			if ($request_url == 'admin/login/' || $request_url == 'admin/logout/')
				$form->setRedirect('/' . $base_url . 'admin/');
		}
		else
			$form->appendError('Username and password combination is incorrect');
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
