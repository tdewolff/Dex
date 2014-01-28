<?php

if (!User::loggedIn())
    user_error('Forbidden access', ERROR);

if (API::action('delete_page'))
{
    if (!API::has('link_id'))
        user_error('No link ID set', ERROR);

    $link_id = Db::escape(API::get('link_id'));
    Db::exec("
        DELETE FROM content WHERE link_id = '" . $link_id . "';
        DELETE FROM link WHERE link_id = '" . $link_id . "';
    ");
    API::finish();
}
else if (API::action('edit_data'))
{
    if (!API::has('link_id'))
        user_error('No link ID set', ERROR);

    if (!API::has('which') || !(
        API::get('which') === 'title' ||
        API::get('which') === 'link'
    ))
        user_error('No known element changed', ERROR);

    if (!API::has('value'))
        user_error('No value detected', ERROR);

    Db::exec("
        UPDATE link
        SET " . (
            API::get('which') === 'title' ? 'title' : 'url'
        ) . " = '" . Db::escape(API::get('value')) . "'
        WHERE link_id = '" . Db::escape(API::get('link_id')) . "';
    ");
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

        $row['content'] = array();
        $table2 = Db::query("SELECT * FROM content WHERE link_id = '" . $row['link_id'] . "';");
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
