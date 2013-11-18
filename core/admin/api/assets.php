<?php

$dir = '';
if (API::has('dir'))
    $dir = API::get('dir');

if (!file_exists('assets/' . $dir))
    user_error('Directory "assets/' . $dir . '" doesn\'t exist', ERROR);

// upload file
if (isset($_FILES['upload']))
{
    if ($_FILES['upload']['error'] != 0)
        API::error('Unknown error');

    $name = $_FILES['upload']['name'];
    $last_slash = strrpos($name, '/');
    $title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
    $extension = strtolower(substr($name, strrpos($name, '.') + 1));

    if (!Resource::isResource($extension))
        API::error('Wrong extension');

    if (file_exists('assets/' . $dir . $name))
        API::error('Already exists');

    if (!move_uploaded_file($_FILES['upload']['tmp_name'], 'assets/' . $dir . $name))
        API::error('Unknown error');

    $width = 0;
    if (Resource::isImage($extension))
        list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);

    API::set('file', array(
        'url' => '/' . $base_url . 'res/assets/' . $dir . $name,
        'name' => $name,
        'icon' => (file_exists('core/resources/images/icons/' . $extension . '.png') ? '/' . $base_url . 'res/core/images/icons/' . $extension . '.png' : '/' . $base_url . 'res/core/images/icons/unknown.png'),
        'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
        'isImage' => Resource::isImage($extension),
        'width' => $width,
        'widthAttr' => Resource::imageSizeAttributes('res/assets/' . $dir . $name, 200),
        'size' => Common::formatBytes(filesize('assets/' . $dir . $name), 2)
    ));
    API::finish();
}

if (API::has('name'))
{
    if (API::action('delete_file'))
    {
        unlink('assets/' . $dir . API::get('name'));
        API::finish();
    }
    else if (API::action('create_directory'))
    {
        if (!preg_match('/[a-zA-Z_0-9]+/', API::get('name')))
            API::error('May only contain alphanumeric characters');

        mkdir('assets/' . $dir . API::get('name'), 0777);
        API::set('directory', array(
            'url' => '/' . $base_url . 'res/assets/' . $dir . API::get('name'),
            'name' => API::get('name'),
            'icon' => 'folder.png',
            'title' => API::get('name')
        ));
        API::finish();
    }
}

$directories = array();
$assets = array();
$images = array();

$handle = opendir('assets/' . $dir);
while (($name = readdir($handle)) !== false)
{
    if (is_file('assets/' . $dir . $name))
    {
        $last_slash = strrpos($name, '/');
        $title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
        $extension = substr($name, strrpos($name, '.') + 1);

        $isImage = Resource::isImage($extension);
        if ($isImage)
        {
            list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);
            $images[] = array(
                'id' => count($images),
                'url' => 'res/assets/' . $dir . $name,
                'name' => $name,
                'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
                'width' => $width
            );
        }
        else
            $assets[] = array(
                'id' => count($assets),
                'url' => $dir . $name,
                'name' => $name,
                'icon' => (file_exists('core/resources/images/icons/' . $extension . '.png') ? $extension . '.png' : 'unknown.png'),
                'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
                'size' => Common::formatBytes(filesize('assets/' . $dir . $name), 2)
            );
    }
    else if (is_dir('assets/' . $dir . $name) && $name != '.')
    {
        $url = $dir . $name . '/';
        if ($name == '..')
        {
            if (empty($dir))
                continue;

            $url = substr($dir, 0, strlen($dir) - 1);
            $last_slash = strrpos($url, '/');
            if ($last_slash !== false)
                $url = substr($url, 0, $last_slash);
            else
                $url = '';
            $name = '..';
        }

        $directories[] = array(
            'id' => count($directories),
            'url' => $url,
            'name' => $name,
            'icon' => ($name == '..' ? 'dirup.png' : 'folder.png'),
            'title' => $name
        );
    }
}

API::set('directories', $directories);
API::set('assets', $assets);
API::set('images', $images);
API::finish();

?>