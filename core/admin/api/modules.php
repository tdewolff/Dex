<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (API::action('enable_module') || API::action('disable_module'))
{
    if (!API::has('module_name'))
        user_error('No module name set', ERROR);

    $db->exec("
        UPDATE module SET enabled = '" . $db->escape(API::action('enable_module') ? '1' : '0') . "'
        WHERE module_name = '" . $db->escape(API::get('module_name')) . "';");
    API::finish();
}

$modules = array();
$table = $db->query("SELECT * FROM module;");
while ($row = $table->fetch())
{
    $ini_filename = 'modules/' . $row['module_name'] . '/config.ini';
    $row['module_id'] = count($modules);
    if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
    {
        $row['title'] = Common::tryOrEmpty($ini, 'title');
        $row['author'] = Common::tryOrEmpty($ini, 'author');
        $row['description'] = Common::tryOrEmpty($ini, 'description');
    }
    $modules[] = $row;
}

API::set('modules', $modules);
API::finish();

?>