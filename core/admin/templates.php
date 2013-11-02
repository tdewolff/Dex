<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

$templates = array();
$handle = opendir('templates/');
while (($name = readdir($handle)) !== false)
    if (is_dir('templates/' . $name) && $name != '.' && $name != '..')
    {
        $ini_filename = 'templates/' . $name . '/config.ini';
        if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
        {
            $row = array();
            $row['title'] = Common::tryOrEmpty($ini, 'title');
            $row['author'] = Common::tryOrEmpty($ini, 'author');
            $row['description'] = Common::tryOrEmpty($ini, 'description');
            $templates[] = $row;
        }
    }

Core::addStyle('popbox.css');
Core::addStyle('dropdown.css');

Hooks::emit('admin_header');

Core::assign('templates', $templates);
Core::render('admin/templates.tpl');

Hooks::emit('admin_footer');
exit;

?>