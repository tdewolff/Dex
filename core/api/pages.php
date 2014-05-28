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
	require_once('include/form.class.php');

	if (!API::has('pages'))
		user_error('No pages set', ERROR);

	$pages = API::get('pages');

	$errors = array();
	foreach ($pages as $i => $page)
	{
		if (strlen($pages[$i]['url']) && $pages[$i]['url'][strlen($pages[$i]['url']) - 1] != '/')
			$pages[$i]['url'] .= '/';

		if (($error = Form::validateItem($page['title'], array('regex' => '.*', 'min' => 1, 'max' => 25))) !== false)
			$errors[] = array('link_id' => $page['link_id'], 'name' => 'title', 'error' => $error);

		$error = Core::verifyLinkUrl($page['url'], $page['link_id']);
		if ($error !== true && $error != __('Already used'))
			$errors[] = array('link_id' => $page['link_id'], 'name' => 'url', 'error' => $error);

		foreach (API::get('pages') as $page2)
		{
			if ($page2['link_id'] == $page['link_id'])
				break;

			if ($page2['url'] == $page['url'])
			{
				$errors[] = array('link_id' => $page['link_id'], 'name' => 'url', 'error' => __('Duplicate'));
				$errors[] = array('link_id' => $page2['link_id'], 'name' => 'url', 'error' => __('Duplicate'));
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
		Db::exec($query . " COMMIT;");
	}

	API::set('errors', $errors);
	API::finish();
}
else if (API::action('get_pages'))
{
	$pages = array();
	$table = Db::query("SELECT * FROM link ORDER BY url;");
	while ($row = $table->fetch())
	{
		$config = new Config('templates/' . $row['template_name'] . '/template.conf');
		$config->setDefault('has_admin', '1');
		$config->setDefault('has_site_admin', '0');
		$row['has_admin'] = $config->get('has_admin');
		$row['has_site_admin'] = $config->get('has_site_admin');

		$row['template_name'] = $config->get('title');
		$row['title'] = htmlspecialchars($row['title']);

		$row['content'] = '';
		$table2 = Db::query("SELECT content FROM content WHERE link_id = '" . $row['link_id'] . "';");
		while ($row2 = $table2->fetch())
			$row['content'] .= ' ' . $row2['content'];
		$row['content'] = preg_replace('/<[^>]+>/', ' ', $row['content']);
		$row['content'] = trim(preg_replace('/\s{2,}/', ' ', $row['content']));
		$row['content'] = strlen($row['content']) > 140 ? substr($row['content'], 0, 140) . '...' : $row['content'];

		$row['length'] = Common::formatBytes(strlen($row['content']));
		$pages[] = $row;
	}
	API::set('pages', $pages);
	API::finish();
}
