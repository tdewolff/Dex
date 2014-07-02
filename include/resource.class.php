<?php

class Resource
{
	private static $caching = false;

	private static $extensions_mime = array(
		'js' => 'application/x-javascript',
		'css' => 'text/css',
		'htm' => 'text/html',
		'html' => 'text/html',
		'xml' => 'application/xml',
		'txt' => 'text/plain',

		'svg' => 'image/svg+xml',
		'eot' => 'application/vnd.ms-fontobject',
		'woff' => 'application/font-woff',
		'otf' => 'application/octet-stream',
		'ttf' => 'application/x-font-ttf',

		'bmp' => 'image/bmp',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',

		'wav' => 'audio/x-wav',
		'avi' => 'video/x-msvideo',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'mov' => 'video/quicktime',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'swf' => 'application/x-shockwave-flash',

		'doc' => 'application/msword',
		'ppt' => 'application/vnd.ms-powerpoint',
		'xls' => 'application/vnd.ms-excel',
		'pdf' => 'application/pdf',
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'7z' => 'application/x-7z-compressed',
		'tar' => 'application/x-tar',
	);

	public static function setCaching($caching) {
		self::$caching = $caching;
	}

	public static function getMime($extension)
	{
		return self::$extensions_mime[strtolower($extension)];
	}

	public static function isResource($extension)
	{
		return array_key_exists(strtolower($extension), self::$extensions_mime);
	}

	public static function isImage($extension)
	{
		$extension = strtolower($extension);
		return ($extension == 'png' || $extension == 'gif' || $extension == 'jpg' || $extension == 'jpeg'); // only types that can be resized
	}

	public static function getMinified($filename)
	{
		$min = Common::insertMinExtension($filename);
		if (is_file(Common::$base_path . $min) && filemtime(Common::$base_path . $filename) < filemtime(Common::$base_path . $min))
			return $min;

		// SmushIt gif -> png case
		if (substr($filename, strrpos($filename, '.') + 1) == 'gif')
		{
			$min .= '.png';
			if (is_file(Common::$base_path . $min) && filemtime(Common::$base_path . $filename) < filemtime(Common::$base_path . $min))
				$filename = $min;
		}
		return $filename;
	}

	private static function cacheFilename($filenames, $cache_formatted_filename)
	{
		$latest_modify_time = 0;
		foreach ($filenames as $filename)
			if (!is_file(Common::$base_path . $filename))
				user_error('File "' . $filename . '" could not be found', WARNING);
			else
			{
				$modify_time = filemtime(Common::$base_path . $filename);
				$latest_modify_time = max($modify_time, $latest_modify_time);
			}

		$unique_scriptname = implode($filenames) . $latest_modify_time;
		return sprintf($cache_formatted_filename, sha1($unique_scriptname));
	}

	public static function cacheFiles($filenames, $extension)
	{
		$starttime_local = explode(' ', microtime());

		if ($extension == 'js' || $extension == 'css') // use minified js and css when available
			foreach ($filenames as $i => $filename)
				if (is_file(Common::$base_path . Common::insertMinExtension($filename)) &&
					filemtime(Common::$base_path . $filename) < filemtime(Common::$base_path . Common::insertMinExtension($filename)))
					$filenames[$i] = Common::insertMinExtension($filename);

		$cache_filename = self::cacheFilename($filenames, 'cache/%s.' . $extension);
		if (!is_file(Common::$base_path . $cache_filename) || !self::$caching)
		{
			$content = '';
			foreach ($filenames as $filename)
				if (is_file(Common::$base_path . $filename))
					$content .= file_get_contents(Common::$base_path . $filename);
			file_put_contents(Common::$base_path . $cache_filename, $content);

			$endtime_local = explode(' ', microtime());
			$totaltime = ($endtime_local[1] + $endtime_local[0] - $starttime_local[1] - $starttime_local[0]);
			Log::caching($cache_filename . ' took ' . number_format($totaltime, 4) . 's');
		}
		return 'res/' . $cache_filename;
	}

	public static function expandUrl($url)
	{
		$filename = '';
		if (isset($url[1]) && isset($url[2]))
			if ($url[1] == 'cache')
				$filename = 'cache/' . implode('/', array_splice($url, 2));
			else if ($url[1] == 'assets')
				$filename = 'assets/' . implode('/', array_splice($url, 2));
			else if ($url[1] == 'core')
				$filename = 'core/resources/' . implode('/', array_splice($url, 2));
			else if ($url[1] == 'module')
				$filename = 'modules/' . $url[2] . '/resources/' . implode('/', array_splice($url, 3));
			else if ($url[1] == 'theme')
				$filename = 'themes/' . $url[2] . '/resources/' . implode('/', array_splice($url, 3));

		if (empty($filename))
			user_error('Could not expand URL "' . implode('/', $url) . '" to resource', ERROR);

		return $filename;
	}

	public static function imageSize($filename, $max_width, $max_height = 0)
	{
		if (!is_file(Common::$base_path . $filename))
		{
			user_error('Could not find "' . $filename . '" to determine new image size', WARNING);
			return array(0, 0);
		}

		list($width, $height, $mime_type, $attribute) = getimagesize(Common::$base_path . $filename);

		$new_width = $width;
		$new_height = $height;
		if ($width > $max_width && $max_width != 0)
		{
			$new_width = $max_width;
			$new_height = floor($height * ($new_width / $width));
			if ($new_height > $max_height && $max_height != 0)
			{
				$new_height = $max_height;
				$new_width = floor($width * ($new_height / $height));
			}
		}
		else if ($height > $max_height && $max_height != 0)
		{
			$new_height = $max_height;
			$new_width = floor($width * ($new_height / $height));
			if ($new_width > $max_width && $max_width != 0)
			{
				$new_width = $max_width;
				$new_height = floor($height * ($new_width / $width));
			}
		}
		return array($new_width, $new_height);
	}

	public static function imageSizeAttributes($request_url, $max_width, $max_height = 0)
	{
		$filename = self::expandUrl($request_url);
		list($new_width, $new_height) = self::imageSize($filename, $max_width, $max_height);
		return 'width="' . $new_width . '" height="' . $new_height . '"';
	}

	public static function imageResize($filename, $max_width, $max_height = 0)
	{
		$starttime_local = explode(' ', microtime());
		$cache_filename = 'cache/' . sha1($filename . '_' . $max_width . '_' . $max_height . '_' . filemtime(Common::$base_path . $filename)) . '.png';
		if (!is_file(Common::$base_path . $cache_filename))
		{
			list($width, $height, $mime_type, $attribute) = getimagesize(Common::$base_path . $filename);
			list($new_width, $new_height) = self::imageSize($filename, $max_width, $max_height);

			switch (image_type_to_mime_type($mime_type))
			{
				case 'image/jpeg':
					$image = imagecreatefromjpeg(Common::$base_path . $filename);
					break;

				case 'image/png':
					$image = imagecreatefrompng(Common::$base_path . $filename);
					imagealphablending($image, true);
					imagesavealpha($image, true);
					break;

				case 'image/gif':
					$image = imagecreatefromgif(Common::$base_path . $filename);
					break;

				default:
					user_error('Could not resize "' . $filename . '" since it is not a JPEG, PNG or GIF', WARNING);
					return $filename;
			}

			$resizedImage = imagecreatetruecolor($new_width, $new_height);
			imagealphablending($resizedImage, false);
			imagesavealpha($resizedImage, true);
			imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			if (function_exists('imageistruecolor') && !imageistruecolor($image) && imagecolortransparent($image) > 0)
				imagetruecolortopalette($resizedImage, false, imagecolorstotal($image));

			imagepng($resizedImage, $cache_filename);
			imagedestroy($resizedImage);
			imagedestroy($image);

			$endtime_local = explode(' ', microtime());
			$totaltime = ($endtime_local[1] + $endtime_local[0] - $starttime_local[1] - $starttime_local[0]);
			Log::caching($cache_filename . ' resize took ' . number_format($totaltime, 4) . 's (' . $width . 'x' . $height . ' to ' . $new_width . 'x' . $new_height . ')');
		}
		return $cache_filename;
	}
}
