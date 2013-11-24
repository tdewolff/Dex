<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (API::action('get_templates'))
{
    $templates = array();
    $handle = opendir('templates/');
    while (($template_name = readdir($handle)) !== false)
        if (is_dir('templates/' . $template_name) && $template_name != '.' && $template_name != '..')
        {
            $ini_filename = 'templates/' . $template_name . '/config.ini';
            if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
                $templates[] = array(
                    'name' => $template_name,
                    'title' => Common::tryOrEmpty($ini, 'title'),
                    'author' => Common::tryOrEmpty($ini, 'author'),
                    'description' => Common::tryOrEmpty($ini, 'description')
                );
        }
    Common::sortOn($templates, 'name');

    API::set('templates', $templates);
    API::finish();
}

?>