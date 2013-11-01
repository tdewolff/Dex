<?php

if (isset($url[2]) && $url[2] == 'destroy' && isset($url[3]))
	;// remove file

$dir = '';

$assets = array();
$directories = array();
$handle = opendir('assets/' . $dir);
while (($name = readdir($handle)) !== false)
	if ($name != '.gitignore' && is_file('assets/' . $dir . $name))
	{
		$last_slash = strrpos($name, '/');
		$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));

		list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);

		$assets[] = array(
			'url' => 'res/assets/' . $dir . $name,
			'title' => $title,
			'width' => $width,
			'height' => $height
		);
	}
	else if (is_dir('assets/' . $dir . $name))
	{
		$directories[] = $dir . $name;
	}

Hooks::emit('admin_header');

Core::assign('assets', $assets);
Core::assign('directories', $directories);
Core::render('admin/assets.tpl');

Hooks::emit('admin_footer');
exit;

?>
