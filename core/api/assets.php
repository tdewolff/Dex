<?php

if (!User::loggedIn())
	user_error('Forbidden access', ERROR);

// set current directory
$dir = 'assets/';
if (API::has('dir'))
	$dir .= API::get('dir');
else if (isset($_POST['dir'])) // exception for file upload
	$dir .= $_POST['dir'];
$dir = html_entity_decode(urldecode($dir));
$request_dir = substr($dir, 7);

$dir = preg_replace('/\\\\/', '/', realpath($dir));
$root = dirname($_SERVER['SCRIPT_FILENAME']) . '/assets';
if (strlen($dir) < strlen($root) || substr($dir, 0, strlen($root)) !== $root)
	user_error('Directory "' . $request_dir . '" doesn\'t exist or is outside assets directory', ERROR);

$dir = substr($dir, strlen($root));
if (!empty($dir))
	$dir = 'assets' . $dir . '/';
else
	$dir = 'assets/' . $dir;

if (!is_readable($dir) || !is_dir($dir))
	user_error('Directory "' . $request_dir . '" doesn\'t exist or is not readable', ERROR);


// upload file
if (isset($_FILES['upload']))
{
	if ($_FILES['upload']['error'] != 0)
	{
		if ($_FILES['upload']['error'] == 1 || $_FILES['upload']['error'] == 2)
			API::set('upload_error', 'File too big');
		else
			API::set('upload_error', 'Unknown error: ' . $_FILES['upload']['error']);
		API::finish();
	}

	$name = $_FILES['upload']['name'];
	$slash_position = strrpos($name, '/');
	$dot_position = strrpos($name, '.');
	$title = substr($name, $slash_position ? $slash_position + 1 : 0, $dot_position);
	$extension = strtolower(substr($name, $dot_position + 1));

	if (is_file($dir . $name))
	{
		$i = 1;
		$filename = substr($name, 0, $dot_position);
		while (is_file($dir . $filename . ' (' . $i . ').' . $extension))
			$i++;
		$name = $filename . ' (' . $i . ').' . $extension;
		$title = $filename . ' (' . $i . ')';
	}

	if (!Resource::isResource($extension))
		API::set('upload_error', 'Wrong extension');
	else if (!is_writable($dir))
		API::set('upload_error', 'Directory not writable');
	else if (!move_uploaded_file($_FILES['upload']['tmp_name'], $dir . $name))
		API::set('upload_error', 'Unknown error');
	else
	{
		$width = 0;
		if (Resource::isImage($extension))
			list($width, $height, $type, $attribute) = getimagesize($dir . $name);

		$max_width = (isset($_POST['max_width']) ? $_POST['max_width'] : 0);

		API::set('file', array(
			'url' => $dir . $name,
			'name' => $name,
			'icon' => (is_file('core/resources/images/icons/' . $extension . '.png') ? $extension . '.png' : 'unknown.png'),
			'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
			'size' => Common::formatBytes(filesize($dir . $name), 2),
			'width' => $width,
			'is_image' => Resource::isImage($extension),
			'attr' => Resource::isImage($extension) ? Resource::imageSizeAttributes(explode('/', 'res/' . $dir . $name), $max_width) : ''
		));
	}
	API::finish();
}
else if (API::action('create_directory'))
{
	if (!API::has('name'))
		user_error('No name set', ERROR);

	if (!preg_match('/^[a-zA-Z0-9_\-\s]+$/', API::get('name')))
		API::set('error', 'May only contain alphanumeric or (_-) characters');
	else if (is_dir($dir . API::get('name') . '/'))
		API::set('error', 'Directory "' . $dir . API::get('name') . '" already exists');
	else if (!Common::ensureWritableDir($dir . API::get('name') . '/'))
		API::set('error', 'Directory "' . $dir . API::get('name') . '" could not be created');
	else
		API::set('directory', array(
			'dir' => substr($dir, strlen('assets/')) . API::get('name') . '/',
			'name' => API::get('name'),
			'icon' => 'folder.png',
			'is_deletable' => true
		));
	API::finish();
}
else if (API::action('delete_directory'))
{
	if (!API::has('name'))
		user_error('No name set', ERROR);

	if (!is_dir($dir . API::get('name') . '/'))
		user_error('Directory "' . $dir . API::get('name') . '" doesn\'t exist', ERROR);

	if (!rmdir($dir . API::get('name') . '/'))
		user_error('Directory "' . $dir . API::get('name') . '" could not be deleted', ERROR);

	API::finish();
}
else if (API::action('delete_file'))
{
	if (!API::has('name'))
		user_error('No name set', ERROR);

	if (!is_file($dir . API::get('name')))
		user_error('File "' . $dir . API::get('name') . '" doesn\'t exist', ERROR);

	if (!unlink($dir . API::get('name')))
		user_error('File "' . $dir . API::get('name') . '" could not be deleted', ERROR);

	API::finish();
}
else if (API::action('get_breadcrumbs'))
{
	$url = '';
	$breadcrumbs = array();
	foreach (array_slice(explode('/', $dir), 1) as $breadcrumb)
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
else if (API::action('get_directories') || API::action('get_assets') || API::action('get_directories_assets'))
{
	if (API::action('get_directories') || API::action('get_directories_assets'))
	{
		$directories = array();
		$handle = opendir($dir);
		while (($name = readdir($handle)) !== false)
		{
			if (is_readable($dir . $name) && is_dir($dir . $name) && $name != '.')
			{
  				$empty = true;
				$url = $dir . $name . '/';
				if ($name == '..')
				{
					if ($dir == 'assets/')
						continue;

					$url = substr($dir, 0, strlen($dir) - 1);
					$last_slash = strrpos($url, '/');
					$url = $last_slash ? substr($url, 0, $last_slash + 1) : '';
				}
				else
				{
					$handle_sub = opendir($dir . $name);
					while (($name_sub = readdir($handle_sub)) !== false)
						if ($name_sub != '.' && $name_sub != '..')
						{
  							$empty = false;
							break;
						}
				}

				$directories[] = array(
					'dir' => ($url == 'assets/' ? '' : substr($url, 7)), // remove leading "assets/"
					'name' => $name,
					'icon' => ($name == '..' ? 'dirup.png' : 'folder.png'),
					'is_deletable' => $empty && $name != '..'
				);
			}
		}
		Common::sortOn($directories, 'name');
		API::set('directories', $directories);
	}

	if (API::action('get_assets') || API::action('get_directories_assets'))
	{
		$assets = array();
		$handle = opendir($dir);
		while (($name = readdir($handle)) !== false)
		{
			if (is_file($dir . $name) && !Common::hasMinExtension($name))
			{
				$last_slash = strrpos($name, '/');
				$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
				$extension = substr($name, strrpos($name, '.') + 1);

				if (Resource::isResource($extension) && !Resource::isImage($extension))
				{
					list($width, $height, $type, $attribute) = getimagesize($dir . $name);
					$assets[] = array(
						'url' => $dir . $name,
						'name' => $name,
						'icon' => (is_file('core/resources/images/icons/' . $extension . '.png') ? $extension . '.png' : 'unknown.png'),
						'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
						'size' => Common::formatBytes(filesize($dir . $name), 2),
						'width' => $width
					);
				}
			}
		}
		Common::sortOn($assets, 'name');
		API::set('assets', $assets);
	}
	API::finish();
}
else if (API::action('get_images'))
{
	$max_width = API::has('max_width') ? API::get('max_width') : 0;

	$images = array();
	$handle = opendir($dir);
	while (($name = readdir($handle)) !== false)
	{
		if (is_file($dir . $name) && !Common::hasMinExtension($name))
		{
			$last_slash = strrpos($name, '/');
			$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
			$extension = substr($name, strrpos($name, '.') + 1);

			if (Resource::isImage($extension))
			{
				list($width, $height, $type, $attribute) = getimagesize($dir . $name);
				$images[] = array(
					'url' => $dir . $name,
					'name' => $name,
					'icon' => (is_file('core/resources/images/icons/' . $extension . '.png') ? $extension . '.png' : 'unknown.png'),
					'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
					'size' => Common::formatBytes(filesize($dir . $name), 2),
					'width' => $width,
					'attr' => Resource::imageSizeAttributes(explode('/', 'res/' . $dir . $name), $max_width)
				);
			}
		}
	}
	Common::sortOn($images, 'name');

	API::set('images', $images);
	API::finish();
}

?>