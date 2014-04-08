<?php

$filename = Resource::expandUrl($url);
$extension_position = strrpos($filename, '.');
$extension = ($extension_position === false ? '' : strtolower(substr($filename, $extension_position + 1)));

$parameters_position = strpos($extension, '/');
if ($parameters_position !== false)
{
	$url_parameters = explode('/', substr($extension, $parameters_position + 1));
	$parameters = array();
	foreach ($url_parameters as $parameter)
		if (($value_position = strpos($parameter, '=')))
			$parameters[substr($parameter, 0, $value_position)] = substr($parameter, $value_position + 1);

	$filename = substr($filename, 0, $extension_position + 1 + $parameters_position);
	$extension = substr($extension, 0, $parameters_position);
}

if (!Resource::isResource($extension))
	user_error('Resource file extension "' . $extension . '" invalid of "' . Common::$request_url . '"', ERROR);
else if (!is_file($filename))
	user_error('Could not find resource file "' . $filename . '"', ERROR);
else
{
	if (Resource::isImage($extension))
	{
		$filename = Resource::getMinified($filename);
		if (isset($parameters))
		{
			$w = Common::tryOrZero($parameters, 'w');
			$h = Common::tryOrZero($parameters, 'h');
			$filename = Resource::imageResize($filename, $w, $h);
		}
	}

	header('Cache-Control: public');
	header('Content-Type: ' . Resource::getMime($extension) . '; charset: UTF-8');
	$headers = apache_request_headers();
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($filename)))
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT', true, 304);
	else
	{
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT', true, 200);
		header('Content-Length: ' . filesize($filename));
		print file_get_contents($filename);
	}
}
exit;
