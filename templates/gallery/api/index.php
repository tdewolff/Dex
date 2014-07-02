<?php

if (!API::has('link_id'))
	user_error('No link ID set', ERROR);

$content = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape(API::get('link_id')) . "' AND name = 'settings' LIMIT 1;");
if (!$content)
	user_error('No settings found', ERROR);
$settings = json_decode($content['content'], true);

if (API::action('get_album'))
{
	if (!API::has('album'))
		user_error('No album set', ERROR);

	$album = trim(preg_replace('/\/\./', '', API::get('album')));
	if ($album == '')
		user_error('No album set', ERROR);

	$dir = $settings['directory'] . '/' . $album . '/';
	$max_width = API::has('max_width') ? API::get('max_width') : 0;
	$max_height = API::has('max_height') ? API::get('max_height') : 0;

	if (!is_dir($dir))
		user_error('Directory does not exist', ERROR);

	$images = array();
	if (($handle = opendir($dir)) !== false)
		while (($name = readdir($handle)) !== false)
		{
			if (is_file($dir . $name) && !Common::hasMinExtension($name))
			{
				$filename = Resource::getMinified($name);
				$last_slash = strrpos($name, '/');
				$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
				$extension = substr($name, strrpos($name, '.') + 1);

				if (Resource::isImage($extension))
				{
					list($width, $height, $type, $attribute) = getimagesize($dir . $name);
					$width = min($width, $max_width);
					$height = min($height, $max_height);
					$images[] = array(
						'url' => $dir . $filename,
						'name' => $name,
						'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title),
						'width' => $width,
						'height' => $height,
						'attr' => Resource::imageSizeAttributes(explode('/', 'res/' . $dir . $filename), $max_width, $max_height),
						'mtime' => filemtime($dir . $filename)
					);
				}
			}
		}
	Common::sortOn($images, 'name');
	API::set('images', $images);
	API::finish();
}
else if (API::action('get_albums'))
{
	$dir = $settings['directory'] . '/';
	$max_width = API::has('max_width') ? API::get('max_width') : 0;
	$max_height = API::has('max_height') ? API::get('max_height') : 0;

	if (!is_dir($dir))
		user_error('Directory does not exist', ERROR);

	$albums = array();
	if (($handle = opendir($dir)) !== false)
		while (($name = readdir($handle)) !== false)
		{
			if (is_readable($dir . $name) && is_dir($dir . $name) && $name != '.' && $name != '..')
			{
				$images = array();
				if (($handle_sub = opendir($dir . $name)) !== false)
					while (($name_sub = readdir($handle_sub)) !== false)
						if ($name_sub != '.' && $name_sub != '..' && is_file($dir . $name . '/' . $name_sub) && !Common::hasMinExtension($name_sub) && Resource::isImage(substr($name_sub, strrpos($name_sub, '.') + 1)))
							$images[] = $name_sub;

				if (count($images))
				{
					$image_name = $images[mt_rand(0, count($images)-1)];
					$image_filename = Resource::getMinified($image_name);
					$last_slash = strrpos($image_name, '/');
					$title = substr($image_name, $last_slash ? $last_slash + 1 : 0, strrpos($image_name, '.'));

					list($width, $height, $type, $attribute) = getimagesize($dir . $name . '/' . $image_filename);
					$width = min($width, $max_width);
					$height = min($height, $max_height);
					$albums[] = array(
						'url' => $dir . $name . '/' . $image_filename,
						'name' => $name,
						'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title),
						'width' => $width,
						'height' => $height,
						'attr' => Resource::imageSizeAttributes(explode('/', 'res/' . $dir . $name . '/' . $image_filename), $max_width, $max_height),
						'mtime' => filemtime($dir . $name . '/' . $image_filename)
					);
				}
			}
		}
	Common::sortOn($albums, 'name');
	API::set('albums', $albums);
	API::finish();
}