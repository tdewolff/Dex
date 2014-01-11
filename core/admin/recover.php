<?php

Core::addTitle('Admin panel');
Core::addTitle('Recover');

// information pages
if (isset($url[2]) && ($url[2] == 'sent' || $url[2] == 'success'))
{
	Hooks::emit('admin-header');

	Core::assign($url[2], true);
	Core::render('admin/recover.tpl');

	Hooks::emit('admin-footer');
	exit;
}
// reset password
else if (isset($url[2]) && $url[2] == 'reset')
{
	// even if token doesn't exist, show password change form so hackers never know whether they're right or wrong
	$recover = false;
	if (isset($_GET['i']) && isset($_GET['t']))
	{
		$recover = $db->querySingle("SELECT * FROM recover WHERE recover_id = '" . $db->escape($_GET['i']) . "' LIMIT 1;");
		if (!Bcrypt::verify($_GET['t'], $recover['token']))
			$recover = false;
	}

	$form = new Form('reset');

	$form->addSection('Reset', 'Fill out your new password to reset it.');
	$form->addPassword('password', 'Password', '');
	$form->addPasswordConfirm('password2', 'password', 'Confirm password', '');

	$form->addSeparator();

	$form->setSubmit('<i class="fa fa-refresh"></i>&ensp;Reset');
	$form->setResponse('', 'Not reset');

	if ($form->submitted())
	{
		if ($form->validate())
		{
			if ($recover && $recover['expiry_time'] > time())
			{
				$db->exec("
				UPDATE user SET
					password = '" . $db->escape(Bcrypt::hash($form->get('password'))) . "'
				WHERE user_id = '" . $db->escape($recover['user_id']) . "';");
			}
			else if ($recover)
				$form->appendError('Token has expired, request a new one at the <a href="/' . $_['base_url'] . 'admin/recover/">password recovery</a>.');

			$form->setRedirect('/' . $base_url . 'admin/recover/success/');
		}
		$form->finish();
	}

	if (($recover && $recover['expiry_time'] <= time()) || !isset($_GET['i']) || !isset($_GET['t']) || strlen($_GET['t']) != 24)
	{
		Hooks::emit('admin-header');

		if ($recover && $recover['expiry_time'] <= time())
		{
			$db->exec("DELETE FROM recover WHERE recover_id = '" . $db->escape($recover['recover_id']) . "';");
			Core::assign('expired', true);
		}
		else
			Core::assign('malformed', true);
		Core::render('admin/recover.tpl');

		Hooks::emit('admin-footer');
		exit;
	}

	Hooks::emit('admin-header');

	Core::assign('reset', $form);
	Core::render('admin/recover.tpl');

	Hooks::emit('admin-footer');
	exit;
}
// send email
else
{
	$db->exec("DELETE FROM recover WHERE expiry_time <= '" . $db->escape(time() - (60 * 60 * 24 * 7)) . "';"); // remove tokens a week old

	$form = new Form('recover');

	$form->addSection('Recover', 'Fill out your email address to receive an email containing information on how to recover your password.');
	$form->addEmail('email', 'Email address', '');

	$form->addSeparator();

	$form->setSubmit('<i class="fa fa-reply"></i>&ensp;Recover');
	$form->setResponse('', 'Not sent');

	if ($form->submitted())
	{
		if ($form->validate())
		{
			$user = $db->querySingle("SELECT * FROM user WHERE email = '" . $db->escape($form->get('email')) . "' LIMIT 1;");
			if ($user)
			{
				$token = random(24);
				$db->exec("
				DELETE FROM recover WHERE user_id = '" . $db->escape($user['user_id']) . "';
				INSERT INTO recover (user_id, token, expiry_time) VALUES (
					'" . $db->escape($user['user_id']) . "',
					'" . $db->escape(Bcrypt::hash($token)) . "',
					'" . $db->escape(time() + (60 * 30)) . "'
				);");

				$link = 'http://' . substr($_SERVER['HTTP_HOST'], 4) . '/' . $base_url . 'admin/reset/?i=' . $db->lastId() . '&t=' . urlencode($token);

				require_once('vendor/swift-mailer/swift_required.php');

				$transport = Swift_SmtpTransport::newInstance('localhost', 25);
				$mailer = Swift_Mailer::newInstance($transport);
				$message = Swift_Message::newInstance('Dex password recovery')
					->setFrom(array('noreply@' . $_SERVER['HTTP_HOST']))
					->setTo(array($user['email']))
					->setBody('A password recovery request has been made for \'' . $user['username'] . '\' at ' . $_SERVER['HTTP_HOST'] . "\n" .
						      'Click the link below to change your password. If you did not request a password recovery you can ignore this email.' . "\n\n" .
						      $link . "\n\n" .
						      'Dex');

				if (!$mailer->send($message))
					user_error('Email to \'' . $user['email'] . '\' failed to send', WARNING);
			}

			// even if user doesn't exist, redirect to sent page so hackers never know whether they're right or wrong
			$form->setRedirect('/' . $base_url . 'admin/recover/sent/');
		}
		$form->finish();
	}

	Hooks::emit('admin-header');

	Core::assign('recover', $form);
	Core::render('admin/recover.tpl');

	Hooks::emit('admin-footer');
	exit;
}

?>
