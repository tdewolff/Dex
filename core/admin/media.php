<?php

if (isset($url[2]) && $url[2] == 'destroy' && isset($url[3]))
	;// remove file

$media = array();
$handle = opendir('media/');
while (($media_name = readdir($handle)) !== false)
	if (is_file('media/' . $media_name))
	{
		$last_slash = strrpos($media_name, '/');
		$title = substr($media_name, $last_slash ? $last_slash + 1 : 0, strrpos($media_name, '.'));

		list($width, $height, $type, $attribute) = getimagesize('media/' . $media_name);

		$media[] = array(
			'url' => 'res/media/' . $media_name,
			'title' => $title,
			'width' => $width,
			'height' => $height
		);
	}

Hooks::emit('admin_header');

Core::assign('media', $media);
Core::render('admin/media.tpl');

Hooks::emit('admin_footer');
exit;

?>
