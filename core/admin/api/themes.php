<?php

if (API::action('change_theme'))
{
    if (!API::has('theme_name'))
        user_error('No theme name set', ERROR);

    $db->exec("UPDATE setting SET value = '" . $db->escape(API::get('theme_name')) . "' WHERE key = 'theme';");
    API::finish();
}

$current_theme = '';
if ($theme = $db->querySingle("SELECT * FROM setting WHERE key = 'theme';"))
    $current_theme = $theme['value'];

$themes = array();
$handle = opendir('themes/');
while (($theme_name = readdir($handle)) !== false)
    if (is_dir('themes/' . $theme_name) && $theme_name != '.' && $theme_name != '..')
    {
        $ini_filename = 'themes/' . $theme_name . '/config.ini';
        if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
            $themes[] = array(
                'name' => $theme_name,
                'title' => isset($ini['title']) ? $ini['title'] : '',
                'author' => isset($ini['author']) ? $ini['author'] : '',
                'current' => ($theme_name == $current_theme)
            );
    }

API::set('themes', $themes);
API::finish();

?>