<?php

if (!User::loggedIn())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('delete_page'))
{
	if (!API::has('link_id'))
		user_error('No link ID set', ERROR);

	$link_id = Db::escape(API::get('link_id'));
	Db::exec("BEGIN;
		DELETE FROM content WHERE link_id = '" . $link_id . "';
		DELETE FROM link WHERE link_id = '" . $link_id . "';
	COMMIT;");
	API::finish();
}
else if (API::action('edit_pages'))
{
	require_once('include/dex.class.php');

	if (!API::has('pages'))
		user_error('No pages set', ERROR);

	$pages = API::get('pages');

	$errors = array();
	foreach ($pages as $i => $page)
	{
		if (strlen($pages[$i]['url']) && $pages[$i]['url'][strlen($pages[$i]['url']) - 1] != '/')
			$pages[$i]['url'] .= '/';

		$error = Core::verifyLinkUrl($page['url'], $page['link_id']);
		if ($error !== true && $error != 'Already used')
			$errors[] = array('link_id' => $page['link_id'], 'error' => $error);

		foreach (API::get('pages') as $page2)
		{
			if ($page2['link_id'] == $page['link_id'])
				break;

			if ($page2['url'] == $page['url'])
			{
				$errors[] = array('link_id' => $page['link_id'], 'error' => 'Duplicate');
				$errors[] = array('link_id' => $page2['link_id'], 'error' => 'Duplicate');
			}
		}
	}

	if (!count($errors))
	{
		$template_names = array();
		$table = Db::query("SELECT * FROM link;");
		while ($row = $table->fetch())
			$template_names[$row['link_id']] = $row['template_name'];

		$query = "BEGIN; DELETE FROM link;";
		foreach ($pages as $page)
			$query .= "
			INSERT INTO link (link_id, url, title, template_name) VALUES (
				'" . Db::escape($page['link_id']) . "',
				'" . Db::escape($page['url']) . "',
				'" . Db::escape($page['title']) . "',
				'" . Db::escape($template_names[$page['link_id']]) . "'
			);";
		Db::exec($query . "COMMIT;");
	}

	API::set('errors', $errors);
	API::finish();
}
else if (API::action('get_pages'))
{
	$pages = array();
	$table = Db::query("SELECT * FROM link;");
	while ($row = $table->fetch())
	{
		$ini_filename = 'templates/' . $row['template_name'] . '/config.ini';
		if (is_file($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
			$row['template_name'] = Common::tryOrEmpty($ini, 'title');

		$row['title'] = htmlspecialchars($row['title']);

		$row['content'] = array();
		$table2 = Db::query("SELECT content FROM content WHERE link_id = '" . $row['link_id'] . "';");
		while ($row2 = $table2->fetch())
			$row['content'][] = $row2['content'];
		$row['content'] = strip_tags(implode(' ', $row['content']));
		$row['content'] = strlen($row['content']) > 100 ? substr($row['content'], 0, 100) . '...' : $row['content'];

		$row['length'] = Common::formatBytes(strlen($row['content']));
		$pages[] = $row;
	}
	API::set('pages', $pages);
	API::finish();
}
