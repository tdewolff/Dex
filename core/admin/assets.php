<?php

$dir = implode('/', array_slice($url, 2));
if (strlen($dir))
	$dir .= '/';

if (!file_exists('assets/' . $dir))
	user_error('Directory "assets/' . $dir . '" doesn\'t exist', ERROR);

if (Common::isMethod('POST'))
{
	if (isset($_FILES['upload']))
	{
		if ($_FILES['upload']['error'] != 0)
			Common::ajaxError('Unknown error');

		$name = $_FILES['upload']['name'];
		$last_slash = strrpos($name, '/');
		$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
		$extension = strtolower(substr($name, strrpos($name, '.') + 1));

	    if (!Common::isResource($extension))
	        Common::ajaxError('Wrong extension');

	    if (file_exists('assets/' . $dir . $name))
	        Common::ajaxError('Already exists');

	    if (!move_uploaded_file($_FILES['upload']['tmp_name'], 'assets/' . $dir . $name))
	        Common::ajaxError('Unknown error');

    	$width = 0;
    	if (Common::isImage($extension))
    		list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);

        echo json_encode(array(
        	'status' => 'success',
			'url' => '/' . $base_url . 'res/assets/' . $dir . $name,
			'name' => $name,
        	'icon' => (file_exists('core/resources/images/icons/' . $extension . '.png') ? '/' . $base_url . 'res/core/images/icons/' . $extension . '.png' : '/' . $base_url . 'res/core/images/icons/unknown.png'),
			'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
        	'isImage' => Common::isImage($extension),
			'width' => $width,
			'widthAttr' => Common::imageSizeAttributes('res/assets/' . $dir . $name, 200),
			'size' => Common::formatBytes(filesize('assets/' . $dir . $name), 2)
        ));
		exit;
	}

	$data = Common::getMethodData();
	if (!isset($data['asset_name']))
		Common::ajaxError('No asset name set');

	unlink('assets/' . $dir . $data['asset_name']);
	exit;
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

		$isImage = Common::isImage($extension);
		if ($isImage)
		{
			list($width, $height, $type, $attribute) = getimagesize('assets/' . $dir . $name);
			$images[] = array(
				'url' => 'res/assets/' . $dir . $name,
				'name' => $name,
				'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
				'width' => $width
			);
		}
		else
			$assets[] = array(
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
			'url' => $url,
			'icon' => ($name == '..' ? 'dirup.png' : 'folder.png'),
			'title' => $name
		);
	}
}

Core::addStyle('upload.css');
Core::addDeferredScript('jquery.ui.widget.js');
Core::addDeferredScript('jquery.iframe-transport.js');
Core::addDeferredScript('jquery.fileupload.js');
Core::addDeferredScript('jquery.knob.js');
Core::addDeferredScript('upload.defer.js');

Hooks::emit('admin_header');

Core::assign('directories', $directories);
Core::assign('assets', $assets);
Core::assign('images', $images);
Core::render('admin/assets.tpl');

Hooks::emit('admin_footer');
exit;

?>
