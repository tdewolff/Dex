<?php

// upload file
if (isset($_FILES['upload']))
{
    if ($_FILES['upload']['error'] != 0)
    {
        if ($_FILES['upload']['error'] == 1 || $_FILES['upload']['error'] == 2)
            user_error('File too big', ERROR);
        else
            user_error('Unknown error: ' . $_FILES['upload']['error'], ERROR);
    }

    $name = $_FILES['upload']['name'];
    $last_slash = strrpos($name, '/');
    $title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
    $extension = strtolower(substr($name, strrpos($name, '.') + 1));

    if (!Resource::isResource($extension))
        user_error('Wrong extension', ERROR);

    if (file_exists('assets/' . $dir . $name))
        user_error('Already exists', ERROR);

    if (!move_uploaded_file($_FILES['upload']['tmp_name'], 'assets/' . $dir . $name))
        user_error('Unknown error', ERROR);

    $width = 0;
    if (Resource::isImage($extension))
        list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);

    API::set('file', array(
        'url' => '/' . $base_url . 'res/assets/' . $dir . $name,
        'name' => $name,
        'icon' => (file_exists('core/resources/images/icons/' . $extension . '.png') ? '/' . $base_url . 'res/core/images/icons/' . $extension . '.png' : '/' . $base_url . 'res/core/images/icons/unknown.png'),
        'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
        'size' => Common::formatBytes(filesize('assets/' . $dir . $name), 2),
        'width' => $width,
        'is_image' => Resource::isImage($extension)
    ));
    API::finish();
}

// set current directory
$dir = '';
if (API::has('dir'))
    $dir = API::get('dir');

if (!file_exists('assets/' . $dir))
    user_error('Directory "assets/' . $dir . '" doesn\'t exist', ERROR);


if (API::action('create_directory'))
{
    if (!API::has('name'))
        user_error('No name set', ERROR);

    if (!preg_match('/[a-zA-Z_0-9]+/', API::get('name')))
        user_error('May only contain alphanumeric characters', ERROR);

    mkdir('assets/' . $dir . API::get('name'), 0777);
    API::set('directory', array(
        'dir' => '/' . $base_url . 'res/assets/' . $dir . API::get('name'),
        'name' => API::get('name'),
        'icon' => 'folder.png'
    ));
    API::finish();
}
else if (API::action('delete_file'))
{
    if (!API::has('name'))
        user_error('No name set', ERROR);

    unlink('assets/' . $dir . API::get('name'));
    API::finish();
}
else if (API::action('get_breadcrumbs'))
{
    $breadcrumbs = array();
    $breadcrumbs[] = array(
        'dir' => '',
        'name' => 'Assets'
    );

    $url = '';
    foreach (explode('/', $dir) as $breadcrumb)
        if (!empty($breadcrumb))
        {
            $url .= $breadcrumb . '/';
            $breadcrumbs[] = array(
                'dir' => $url,
                'name' => $breadcrumb
            );
        }

    API::set('breadcrumbs', $breadcrumbs);
    API::finish();
}
else if (API::action('get_directories'))
{
    $directories = array();
    $handle = opendir('assets/' . $dir);
    while (($name = readdir($handle)) !== false)
    {
        if (is_dir('assets/' . $dir . $name) && $name != '.')
        {
            $url = $dir . $name . '/';
            if ($name == '..')
            {
                if (empty($dir))
                    continue;

                $url = substr($dir, 0, strlen($dir) - 1);
                $last_slash = strrpos($url, '/');
                $url = $last_slash ? substr($url, 0, $last_slash) : '';
                $name = '..';
            }

            $directories[] = array(
                'dir' => $url,
                'name' => $name,
                'icon' => ($name == '..' ? 'dirup.png' : 'folder.png')
            );
        }
    }
    Common::sortOn($directories, 'name');

    API::set('directories', $directories);
    API::finish();
}
else if (API::action('get_assets'))
{
    $assets = array();
    $handle = opendir('assets/' . $dir);
    while (($name = readdir($handle)) !== false)
    {
        if (is_file('assets/' . $dir . $name))
        {
            $last_slash = strrpos($name, '/');
            $title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
            $extension = substr($name, strrpos($name, '.') + 1);

            list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);
            $assets[] = array(
                'url' => 'res/assets/' . $dir . $name,
                'name' => $name,
                'icon' => (file_exists('core/resources/images/icons/' . $extension . '.png') ? $extension . '.png' : 'unknown.png'),
                'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
                'size' => Common::formatBytes(filesize('assets/' . $dir . $name), 2),
                'width' => $width,
                'is_image' => Resource::isImage($extension)
            );
        }
    }
    Common::sortOn($assets, 'name');

    API::set('assets', $assets);
    API::finish();
}

?>