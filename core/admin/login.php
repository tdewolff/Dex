<?php

$form = new Form('login');
$form->usePUT();
$form->setRedirect('/' . $base_url . $request_url);
if ($request_url == 'admin/logout/')
	$form->setRedirect('/' . $base_url . 'admin/');

$form->addSection('Login', 'You must login before you can continue to the admin panel.');
$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
$form->addPassword('password', 'Password', '');

$form->addSeparator();
$form->addSubmit('login', '<i class="icon-signin"></i>&ensp;Login', '', '(couldn\'t login)');

if ($form->submittedBy('login'))
{
	if ($form->validateInput())
	{
		$account = $db->querySingle('SELECT * FROM account WHERE username = "' . $db->escape($form->get('username')) . '" LIMIT 1;');
		if ($account && $bcrypt->verify($form->get('password'), $account['password']))
		{
			Session::logIn($account['account_id'], $account['permission']);
			$form->unsetSession(); // don't remember!
		}
		else
			$form->appendError('Username and password combination is incorrect');
	}
	$form->returnJSON();
}

Core::addTitle('Admin panel');

Hooks::emit('admin_header');

Core::assign('login', $form);
Core::render('admin/login.tpl');

Hooks::emit('admin_footer');
exit;

?>
