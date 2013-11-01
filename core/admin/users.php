<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (!isset($url[2]))
{
	if (Common::isMethod('POST'))
	{
		$data = Common::getMethodData();
		if (!isset($data['account_id']) || $data['account_id'] == Session::getAccountId() || !Session::isAdmin())
			user_error('No account ID set, account ID equals current user account ID or current user is no admin', ERROR);

		$db->exec("DELETE FROM account WHERE account_id = '" . $db->escape($data['account_id']) . "';");
		exit;
	}

	$users = array();
	$table = $db->query("SELECT * FROM account;");
	while ($row = $table->fetch())
	{
		$row['permission'] = ucfirst($row['permission']);
		$users[] = $row;
	}

	Core::addStyle('popbox.css');
	Core::addStyle('dropdown.css');

	Hooks::emit('admin_header');

	Core::assign('users', $users);
	Core::render('admin/users.tpl');

	Hooks::emit('admin_footer');
	exit;
}
else
{
    if ($url[2] != 'new')
    {
		$account = $db->querySingle("SELECT * FROM account WHERE account_id = '" . $db->escape($url[2]) . "' LIMIT 1;");
		if (!$account)
			user_error('Account with id "' . $url[2] . '" doesn\'t exist', ERROR);
	}

	$form = new Form('user');
	$form->addSection('User', 'These users can access the admin area. Admins have access to everything.');
	$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
	$form->addPassword('password', 'Password', ($url[2] != 'new' ? 'Leave empty to keep current' : ''));
	$form->addPasswordConfirm('password2', 'password', 'Password', 'Confirm');
	$form->addDropdown('permission', 'Permission level', 'Restricts access', array('admin' => 'Admin', 'user' => 'User'));

    if ($url[2] != 'new')
		$form->allowEmptyTogether(array('password', 'password2'));

	$form->addSeparator();
    if ($url[2] != 'new')
		$form->addPassword('current_password', 'Admin password', 'Confirm with your current password');
	$form->addSubmit('user', '<i class="icon-save"></i>&ensp;Save', '<span class="passed_time">(saved <span></span>)</span>', '(not saved)');

	if ($form->submittedBy('user'))
	{
		if ($form->validateInput())
		{
			$current_account = $db->querySingle("SELECT * FROM account WHERE account_id = '" . $db->escape(Session::getAccountId()) . "' LIMIT 1;");
            if ($db->querySingle("SELECT * FROM account WHERE username = '" . $db->escape($form->get('username')) . "' AND account_id != '" . $db->escape($url[2]) . "' LIMIT 1;"))
                $form->setError('username', 'Already used');
            else if (!$current_account)
                $form->appendError('Unknown error');
            else if ($url[2] != 'new' && !$bcrypt->verify($form->get('current_password'), $current_account['password']))
                $form->setError('current_password', 'Wrong password');
            else if ($url[2] != 'new' && Session::getAccountId() == $url[2] && $current_account['permission'] != $form->get('permission'))
                $form->setError('permission', 'Can\'t change your own permission level');
            else
				if ($url[2] != 'new')
				{
					if ($form->get('password') != '')
						$db->exec("
						UPDATE account SET
							username = '" . $db->escape($form->get('username')) . "',
							password = '" . $db->escape($bcrypt->hash($form->get('password'))) . "',
							permission = '" . $db->escape($form->get('permission')) . "'
						WHERE account_id = '" . $db->escape($url[2]) . "';");
					else
						$db->exec("
						UPDATE account SET
							username = '" . $db->escape($form->get('username')) . "',
							permission = '" . $db->escape($form->get('permission')) . "'
						WHERE account_id = '" . $db->escape($url[2]) . "';");
				}
				else
				{
					$db->exec("
					INSERT INTO account (username, password, permission) VALUES (
						'" . $db->escape($form->get('username')) . "',
						'" . $db->escape($bcrypt->hash($form->get('password'))) . "',
						'" . $db->escape($form->get('permission')) . "'
					);");
					$form->setRedirect('/' . $base_url . 'admin/users/');
				}
		}
		$form->returnJSON();
	}

	if ($url[2] != 'new')
	{
		$form->set('username', $account['username']);
		$form->set('permission', $account['permission']);
	}

	Hooks::emit('admin_header');

	Core::assign('user', $form);
	Core::render('admin/user.tpl');

	Hooks::emit('admin_footer');
	exit;
}

?>
