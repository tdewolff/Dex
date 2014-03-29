<?php

Resource::setCaching($config['caching']);

$filename = Resource::expandUrl($url);

// check extension
$extension_position = strrpos($filename, '.');
$extension = strtolower($extension_position === false ? '' : strtolower(substr($filename, $extension_position + 1)));

if (!Resource::isResource($extension))
	user_error('Resource file extension "' . $extension . '" invalid of "' . Common::$request_url . '"', ERROR);
else if (!is_file($filename))
	user_error('Could not find resource file "' . $filename . '"', ERROR);
else
{
	if (Resource::isImage($extension))
	{
		if (is_file(Common::insertMinExtension($filename)) && filemtime($filename) < filemtime(Common::insertMinExtension($filename)))
			$filename = Common::insertMinExtension($filename);

		if (isset($_GET['w']) || isset($_GET['h']) || isset($_GET['s']))
		{
			// resize images
			$w = Common::tryOrZero($_GET, 'w');
			$h = Common::tryOrZero($_GET, 'h');
			$s = Common::tryOrZero($_GET, 's');
			$filename = Resource::imageResize($filename, $w, $h, $s);
		}
	}

	header('Content-Type: ' . Resource::getMime($extension));
	echo file_get_contents($filename);
}
exit;

?>