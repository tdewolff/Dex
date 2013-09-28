<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (!isset($uri[2]) || $uri[2] == 'remove')
{
	if (isset($uri[2]) && $uri[2] == 'remove' && isset($uri[3]))
		$db->exec("DELETE FROM `accounts` WHERE id = '" . $db->escape($uri[3]) . "';");

	$users = array();
	$table = $db->query("SELECT id, username, userlevel FROM `accounts`;");
	while ($row = $table->fetch())
	{
		$row['userlevel'] = $row['userlevel'] == '0' ? 'Admin' : 'User';
		$users[] = $row;
	}

	Dexterous::addStyle('resources/styles/popbox.css');
	Dexterous::addStyle('resources/styles/dropdown.css');
	Dexterous::addDeferredScript('resources/scripts/popbox.js');
	Dexterous::addDeferredScript('resources/scripts/dropdown.js');

	Hooks::emit('header');

	Dexterous::assign('users', $users);
	Dexterous::render('admin/users.tpl');

	Hooks::emit('footer');
	exit;
}
else
{
	$form = new Form('user', 'Edit user');
	$form->addSection('User', 'These users can access the admin area. Only admins can access all of the admin panel and are meant for webhosters / webdesigners. Regular accounts are for editors.');
	$form->addText('username', 'Username', '', array('[a-zA-Z0-9\-_\.]*', 3, 20, 'May contain alphanumeric characters and (-_.)'));
	$form->addPassword('password', 'Password', ($uri[2] != 'new' ? 'Leave empty to keep current' : ''));
	$form->addPasswordConfirm('password2', 'password', 'Password', 'Confirm');
	$form->addDropdown('userlevel', 'Userlevel', 'Restricts access', array('0' => 'Admin', '1' => 'User'));
	$form->allowEmptyTogether(array('current_password', 'password', 'password2'));

	$form->addSeparator();
	$form->addSubmit('user', '<i class="icon-save"></i>&ensp;Save');

	if ($form->submittedBy('user'))
	{
		if ($form->verifyPost())
		{
            if ($db->querySingle("SELECT * FROM `accounts` WHERE username = '" . $db->escape($form->get('username')) . "' AND id != '" . $db->escape($uri[2]) . "' LIMIT 1;"))
                $form->setError('username', 'Already used');
            else
            {
				if ($uri[2] != 'new')
				{
					if ($form->get('password') != '')
						$db->exec("
						UPDATE `accounts` SET
							username = '" . $db->escape($form->get('username')) . "',
							password = '" . $db->escape($bcrypt->hash($form->get('password'))) . "',
							userlevel = '" . $db->escape($form->get('userlevel')) . "'
						WHERE id = '" . $db->escape($uri[2]) . "';");
					else
						$db->exec("
						UPDATE `accounts` SET
							username = '" . $db->escape($form->get('username')) . "',
							userlevel = '" . $db->escape($form->get('userlevel')) . "'
						WHERE id = '" . $db->escape($uri[2]) . "';");
				}
				else
				{
					$db->exec("
					INSERT INTO `accounts` (username, password, userlevel) VALUES (
						'" . $db->escape($form->get('username')) . "',
						'" . $db->escape($bcrypt->hash($form->get('password'))) . "',
						'" . $db->escape($form->get('userlevel')) . "'
					);");

					Dexterous::assign('form_action', $base_url . 'admin/accounts/' . $db->last_id() . '/');
				}

				$form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
			}
		}
		$form->postToSession();
	}
	else
	{
		if ($uri[2] != 'new')
		{
			$account = $db->querySingle("SELECT * FROM `accounts` WHERE id = '" . $db->escape($uri[2]) . "' LIMIT 1;");
			if ($account === false)
				Hooks::emit('error', 404);

			$form->set('username', $account['username']);
			$form->set('userlevel', $account['userlevel']);
		}
	}

	Hooks::emit('header');

	$form->sessionToForm();
	$form->setupForm($smarty);

	Dexterous::render('admin/user.tpl');

	Hooks::emit('footer');
	exit;
}

?>
