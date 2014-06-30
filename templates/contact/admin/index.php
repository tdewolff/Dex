<?php

if (!isset($url[2]))
	user_error('No link_id set', ERROR);

$link = Db::singleQuery("SELECT * FROM link WHERE link_id = '" . Db::escape($url[2]) . "' LIMIT 1");
if (!$link)
	user_error('Page with link_id "' . $url[2] . '" not found', ERROR);

$form = new Form('settings');

$form->addSection(__('Settings'), '');

$form->addSeparator();
$form->setResponse(__('Saved'), __('Not saved'));

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
	$form->finish();
}

if ($settings = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape($url[2]) . "' AND name = 'settings' LIMIT 1;"))
	foreach (json_decode($settings['content'], true) as $key => $value)
		$form->set($key, $value);

Hooks::emit('admin-header');

Template::set('url', $link['url']);
Template::set('settings', $form);
Template::render('admin/index.tpl');

Hooks::emit('admin-footer');
exit;