<?php

if (!API::has('link_id'))
	user_error('No link ID set', ERROR);

$content = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape(API::get('link_id')) . "' AND name = 'settings' LIMIT 1;");
if (!$content)
	user_error('No settings found', ERROR);
$settings = json_decode($content['content'], true);

if (API::action('get_album'))
{
	API::set('albums', $albums);
	API::finish();
}