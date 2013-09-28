<?php

if (isset($uri[2]) && $uri[2] == 'destroy' && isset($uri[3]))
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
			'url' => 'media/' . $media_name,
			'title' => $title,
			'width' => $width,
			'height' => $height
		);
	}

Dexterous::addStyle('resources/styles/fancybox.css');
Dexterous::addDeferredScript('resources/scripts/fancybox.js');

Hooks::emit('header');

Dexterous::assign('media', $media);
Dexterous::render('admin/media.tpl');

Hooks::emit('footer');
exit;

?>
