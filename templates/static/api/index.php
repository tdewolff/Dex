<?php

if (!User::loggedIn())
    user_error('Forbidden access', ERROR);


if (API::action('save_page'))
{
    if (!API::has('link_id') || !API::has('content'))
        user_error('No link ID or content set', ERROR);

    $db->exec("
    UPDATE content SET
        content = '" . $db->escape(API::get('content')) . "'
    WHERE link_id = '" . $db->escape(API::get('link_id')) . "' AND name = 'content';");

    API::finish();
}

?>