<?php

if (!User::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (!isset($url[2]))
{
	Core::addStyle('vendor/dropdown.css');

	Hooks::emit('admin-header');
	Core::render('admin/users.tpl');
	Hooks::emit('admin-footer');
	exit;
}
else
{
    if ($url[2] != 'new')
    {
		$user = $db->querySingle("SELECT * FROM user WHERE user_id = '" . $db->escape($url[2]) . "' LIMIT 1;");
		if (!$user)
			user_error('User ID "' . $url[2] . '" doesn\'t exist', ERROR);
	}

	$form = new Form('user');

	$form->addSection('User', 'These users can access the admin area. Admins have access to everything.');
	$form->addText('username', 'Username', '', '', array('[a-zA-Z0-9-_]*', 3, 16, 'Only alphanumeric and (-_) characters allowed'));
	$form->addEmail('email', 'Email address', 'Used for notifications and password recovery');
	$form->addPassword('password', 'Password', ($url[2] != 'new' ? 'Leave empty to keep current' : ''));
	$form->addPasswordConfirm('password2', 'password', 'Confirm password', '');
	$form->addRadios('role', 'Role', 'Restricts access', array('admin' => 'Admin', 'editor' => 'Editor'));

	$form->addSeparator();

    if ($url[2] != 'new')
    {
		$form->addPassword('current_password', 'Current password', 'Confirm with your current password');
		$form->optional(array('password', 'password2'));
    }

	$form->setSubmit('<i class="fa fa-save"></i>&ensp;Save');
	$form->setResponse('Saved<span data-time=""></span>', 'Not saved');

	if ($form->submitted())
	{
		if ($form->validate())
		{
			$current_user = $db->querySingle("SELECT * FROM user WHERE user_id = '" . $db->escape(User::getUserId()) . "' LIMIT 1;");
            if ($db->querySingle("SELECT * FROM user WHERE username = '" . $db->escape($form->get('username')) . "' AND user_id != '" . $db->escape($url[2]) . "' LIMIT 1;"))
                $form->setError('username', 'Already used');
            else if (!$current_user)
                $form->appendError('Unknown error');
            else if ($url[2] != 'new' && !Bcrypt::verify($form->get('current_password'), $current_user['password']))
                $form->setError('current_password', 'Wrong password');
            else if ($url[2] != 'new' && User::getUserId() == $url[2] && $current_user['role'] != $form->get('role'))
                $form->setError('role', 'Can\'t change your own role');
            else
				if ($url[2] != 'new')
				{
					if ($form->get('password') != '')
						$db->exec("
						UPDATE user SET
							username = '" . $db->escape($form->get('username')) . "',
							email = '" . $db->escape($form->get('email')) . "',
							password = '" . $db->escape(Bcrypt::hash($form->get('password'))) . "',
							role = '" . $db->escape($form->get('role')) . "'
						WHERE user_id = '" . $db->escape($url[2]) . "';");
					else
						$db->exec("
						UPDATE user SET
							username = '" . $db->escape($form->get('username')) . "',
							email = '" . $db->escape($form->get('email')) . "',
							role = '" . $db->escape($form->get('role')) . "'
						WHERE user_id = '" . $db->escape($url[2]) . "';");
				}
				else
				{
					$db->exec("
					INSERT INTO user (username, email, password, role) VALUES (
						'" . $db->escape($form->get('username')) . "',
						'" . $db->escape($form->get('email')) . "',
						'" . $db->escape(Bcrypt::hash($form->get('password'))) . "',
						'" . $db->escape($form->get('role')) . "'
					);");
					$form->setRedirect('/' . $base_url . 'admin/users/');
				}
		}
		$form->finish();
	}

	if ($url[2] != 'new')
	{
		$form->set('username', $user['username']);
		$form->set('email', $user['email']);
		$form->set('role', $user['role']);
	}

	Hooks::emit('admin-header');

	Core::assign('user', $form);
	Core::render('admin/user.tpl');

	Hooks::emit('admin-footer');
	exit;
}

?>
