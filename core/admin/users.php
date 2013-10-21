<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (!isset($url[2]) || $url[2] == 'remove')
{
	if (isset($url[2]) && $url[2] == 'remove' && isset($url[3]))
		$db->exec("DELETE FROM account WHERE account_id = '" . $db->escape($url[3]) . "';");

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
	$account = $db->querySingle("SELECT * FROM account WHERE account_id = '" . $db->escape($url[2]) . "' LIMIT 1;");
	if (!$account)
		user_error('Account with id "' . $url[2] . '" doesn\'t exist', ERROR);

	$form = new Form('user');

	$form->addSection('User', 'These users can access the admin area. Admins have access to everything.');
	$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'May contain alphanumeric characters and (-_)'));
	$form->addPassword('password', 'Password', ($url[2] != 'new' ? 'Leave empty to keep current' : ''));
	$form->addPasswordConfirm('password2', 'password', 'Password', 'Confirm');
	$form->addDropdown('permission', 'Permission level', 'Restricts access', array('admin' => 'Admin', 'user' => 'User'));
	//$form->allowEmptyTogether(array('current_password', 'password', 'password2')); // TODO: current password

	$form->addSeparator();
	$form->addSubmit('user', '<i class="icon-save"></i>&ensp;Save');

	if ($form->submittedBy('user'))
	{
		if ($form->verifyPost())
		{
            if ($db->querySingle("SELECT * FROM account WHERE username = '" . $db->escape($form->get('username')) . "' AND account_id != '" . $db->escape($url[2]) . "' LIMIT 1;"))
                $form->setError('username', 'Already used');
            else
            {
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
					$form->setAction('/' . $base_url . 'admin/accounts/' . $db->last_id() . '/');
				}

				$form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
			}
		}
		$form->postToSession();
	}
	else
	{
		if ($url[2] != 'new')
		{
			$form->set('username', $account['username']);
			$form->set('permission', $account['permission']);
		}
	}

	Hooks::emit('admin_header');

	$form->sessionToForm();

	Core::assign('user', $form);
	Core::render('admin/user.tpl');

	Hooks::emit('admin_footer');
	exit;
}

?>
