<?php

Hooks::attach('site-header', -1, function () {
	Core::addDeferredScript('vendor/doT.min.js');
	Core::addDeferredScript('common.js');
	Core::addDeferredScript('api.js');
	Core::addDeferredScript('form.js');
});

Hooks::attach('main', 0, function () {
	require_once('include/form.class.php');

	$link_id = Core::getLinkId();

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
			// Db::exec("BEGIN;
			// 	DELETE FROM content WHERE link_id = '" . Db::escape($url[2]) . "' AND name = 'settings';
			// 	INSERT INTO content (link_id, user_id, name, content, modify_time) VALUES (
			// 		'" . Db::escape($url[2]) . "',
			// 		'" . Db::escape(User::getUserId()) . "',
			// 		'settings',
			// 		'" . Db::escape(json_encode(array(
			// 			'directory' => $form->get('directory')
			// 		))) . "',
			// 		'" . Db::escape(time()) . "'
			// 	);
			// COMMIT;");
	print_r('!!!!!!!!!!!!!!' . $form->submitted());
		$form->finish();
	}

	// if ($settings = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape($url[2]) . "' AND name = 'settings' LIMIT 1;"))
	// 	foreach (json_decode($settings['content'], true) as $key => $value)
	// 		$form->set($key, $value);

	Template::set('contact', $form);
	Template::render('index.tpl');
});
