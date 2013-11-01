<?php

if (isset($url[2]) && $url[2] == 'destroy' && isset($url[3]))
	;// remove file

$dir = '';

$media = array();
$directories = array();
$handle = opendir('media/' . $dir);
while (($name = readdir($handle)) !== false)
	if (is_file('media/' . $dir . $name))
	{
		$last_slash = strrpos($name, '/');
		$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));

		list($width, $height, $type, $attribute) = getimagesize('media/' . $dir . $name);

		$media[] = array(
			'url' => 'res/media/' . $dir . $name,
			'title' => $title,
			'width' => $width,
			'height' => $height
		);
	}
	else if (is_dir('media/' . $dir . $name))
	{
		$directories[] = $dir . $name;
	}

Hooks::emit('admin_header');

Core::assign('media', $media);
Core::assign('directories', $directories);
Core::render('admin/media.tpl');

Hooks::emit('admin_footer');
exit;

?>
