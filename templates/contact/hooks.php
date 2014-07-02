<?php

Hooks::attach('site-header', -1, function () {
	Core::addStyle('vendor/fancybox.css');
	Core::addExternalScript('//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js');
	Core::addDeferredScript('vendor/jquery.fancybox.min.js');
	Core::addDeferredScript('vendor/doT.min.js');
	Core::addDeferredScript('common.js');
	Core::addDeferredScript('api.js');
	Core::addDeferredScript('form.js');
});

Hooks::attach('main', 0, function () {
	require_once('include/form.class.php');

	$link_id = Core::getLinkId();
	$content = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'settings' LIMIT 1;");
	if (!$content)
		user_error('Cannot find settings in database', ERROR);
	$settings = json_decode($content['content'], true);

	$form = new Form('contact');

	$form->addSection(__('Contact'), '');
	$form->addText('title', __('Title'), '', '', array('.*', 3, 50, __('Unknown error')));
	$form->addMultilineText('content', __('Content'), '', '', array('(.|\n)*', 3, 1000, __('Unknown error')));
	$form->addEmail('email', __('Email address'), '');

	$form->addSeparator();
	$form->setSubmit(__('Send'));
	$form->setResponse(__('Sent'), __('Not sent'));

	if ($form->submitted())
	{
		if ($form->validate())
		{
			if ($form->isBot())
				$form->formError('Bot');
			else
			{
				require_once('vendor/swift-mailer/swift_required.php');

				$transport = Swift_SmtpTransport::newInstance('localhost', 25);
				$mailer = Swift_Mailer::newInstance($transport);
				$message = Swift_Message::newInstance($settings['title'] . $form->get('title'))
					->setFrom(array('noreply@' . $_SERVER['HTTP_HOST']))
					->setTo(array($settings['email']))
					->setReplyTo(array($form->get('email')))
					->setBody($form->get('content'));

				if (!$mailer->send($message))
					$form->formError('Email failed to send');
			}
		}
		$form->finish();
	}
	$form->clearSession();

	Template::set('contact', $form);
	Template::render('index.tpl');
});
