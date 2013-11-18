<?php

$form = new Form('login');

$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();

$form->setSubmit('<i class="icon-signin"></i>&ensp;Login');
$form->setResponse('', '(not logged in)');
$form->optional(array('username', 'password'));

if ($form->submitted())
{
	if ($form->validate())
	{
		$account = $db->querySingle('SELECT * FROM account WHERE username = "' . $db->escape($form->get('username')) . '" LIMIT 1;');
		if ($account && $bcrypt->verify($form->get('password'), $account['password']))
		{
			Session::logIn($account['account_id'], $account['permission']);

			$form->clearSession(); // don't remember!
			$form->setRedirect('/' . $base_url . $request_url);
			if ($request_url == 'admin/logout/')
				$form->setRedirect('/' . $base_url . 'admin/');
		}
		else
			$form->appendError('Username and password combination is incorrect');
	}
	$form->finish();
}

Core::addTitle('Admin panel');

Hooks::emit('admin_header');

Core::assign('login', $form);
Core::render('admin/login.tpl');

Hooks::emit('admin_footer');
exit;

?>
