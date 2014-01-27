<?php

if (!User::loggedIn())
    user_error('Forbidden access', ERROR);

use \Michelf\Markdown;
require_once('vendor/smartypants.php');

if (API::action('save_page'))
{
    if (!API::has('link_id') || !API::has('content'))
        user_error('No link ID or content set', ERROR);

    Db::exec("
    INSERT INTO content (link_id, name, content, modify_time) VALUES (
        '" . Db::escape(API::get('link_id')) . "',
        'content',
        '" . Db::escape(SmartyPants(API::get('content'))) . "',
        '" . Db::escape(time()) . "'
    );");

    API::finish();
}

?>