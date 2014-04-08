<?php

if (!User::loggedIn())
	user_error('Forbidden access', ERROR);

if (API::action('save'))
{
	if (!API::has('link_id') || !API::has('content'))
		user_error('No link ID or content set', ERROR);

	$content = API::get('content');
	$content = preg_replace('/([src|href]=")\/' . preg_quote(Common::$base_url, '/') . '/', '\1[base_url]', $content);

	Db::exec("BEGIN;
		DELETE FROM content WHERE link_id = '" . Db::escape(API::get('link_id')) . "' AND name = 'content';
		INSERT INTO content (link_id, user_id, name, content, modify_time) VALUES (
			'" . Db::escape(API::get('link_id')) . "',
			'" . Db::escape(User::getUserId()) . "',
			'content',
			'" . Db::escape($content) . "',
			'" . Db::escape(time()) . "'
		);
	COMMIT;");

	API::finish();
}
