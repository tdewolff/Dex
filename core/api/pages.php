<?php

session_start();
if (!Session::isAdmin())
    user_error('Forbidden access', ERROR);

if (API::action('delete_page'))
{
    if (!API::has('link_id'))
        user_error('No link ID set', ERROR);

    $db->exec("
        DELETE FROM content WHERE link_id = '" . $db->escape(API::get('link_id')) . "';
        DELETE FROM link WHERE link_id = '" . $db->escape(API::get('link_id')) . "';");
    API::finish();
}
else if (API::action('get_pages'))
{
    $pages = array();
    $table = $db->query("SELECT * FROM link;");
    while ($row = $table->fetch())
    {
        $ini_filename = 'templates/' . $row['template_name'] . '/config.ini';
        if (is_file($ini_filename) && ($ini = parse_ini_file($ini_filename)) !== false)
            $row['template_name'] = Common::tryOrEmpty($ini, 'title');

        $row['content'] = array();
        $table2 = $db->query("SELECT * FROM content WHERE link_id = '" . $row['link_id'] . "';");
        while ($row2 = $table2->fetch())
            $row['content'][] = $row2['content'];
        $row['content'] = strip_tags(implode(' ', $row['content']));
        $row['content'] = strlen($row['content']) > 50 ? substr($row['content'], 0, 50) . '...' : $row['content'];
        $row['length'] = Common::formatBytes(strlen($row['content']));
        $pages[] = $row;
    }
    API::set('pages', $pages);
    API::finish();
}

?>