<?php

class Common
{
	private static $caching = false;
	private static $minifying = false;

	public static function setCaching($caching)
	{
		self::$caching = $caching;
	}

	public static function setMinifying($minifying)
	{
		self::$minifying = $minifying;
	}

	public static function validUrl($input)
	{
		// first part are all allowed characters
		// second part makes sure no ../ occurs
		// third part makes sure no more than one / is a t the end
		return !preg_match('/([^a-zA-Z0-9\/\.\-_\?=&]+)|(\.\.\/)|((.+)[\/]{2,}$)/', $input);
	}

	public static function formatBytes($size, $precision = 2)
	{
		if ($size > 0) {
		    $base = log($size) / log(1000);
		    $suffixes = array('', 'k', 'M', 'G', 'T');
		    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)] . 'B';
		}
		return '0 B';
	}

	public static function padIpAddress($ip)
	{
		$bytes = explode('.', $ip);
		foreach ($bytes as $k => $byte)
			$bytes[$k] = str_pad($byte, 3, '0', STR_PAD_LEFT);
		return implode('.', $bytes);
	}

	public static function tryOrEmpty($array, $index)
	{
		return isset($array[$index]) ? $array[$index] : '';
	}

    public static function tryOrZero($array, $index)
    {
        return isset($array[$index]) ? $array[$index] : 0;
    }

	private static function cacheFilename($filenames, $cache_formatted_filename)
	{
		$latest_modify_time = 0;
		foreach ($filenames as $filename)
			if (!file_exists($filename))
				user_error('File "' . $filename . '" could not be found', WARNING);
            else
            {
                $modify_time = filemtime($filename);
                $latest_modify_time = max($modify_time, $latest_modify_time);
            }

		$unique_scriptname = implode($filenames) . $latest_modify_time;
		return sprintf($cache_formatted_filename, sha1($unique_scriptname));
	}

    public static function concatenateFiles($filenames, $extension)
    {
        $starttime_local = explode(' ', microtime());
        $cache_filename = self::cacheFilename($filenames, 'cache/%s.' . $extension);
        if (!file_exists($cache_filename) || self::$caching == false)
        {
            $content = '';
            foreach ($filenames as $filename)
                if (file_exists($filename))
                    $content .= file_get_contents($filename);

            $f = fopen($cache_filename, 'w');
            fwrite($f, $content);
            fclose($f);

            $endtime_local = explode(' ', microtime());
            $totaltime = ($endtime_local[1] + $endtime_local[0] - $starttime_local[1] - $starttime_local[0]);
            Log::caching($cache_filename . ' took ' . number_format($totaltime, 4) . 's');
        }
        return 'res/' . $cache_filename;
    }

	public static function imageResize($filename, $max_width, $max_height, $scale)
	{
        $starttime_local = explode(' ', microtime());
		$cache_filename = 'cache/' . sha1($filename . '_' . $max_width . '_' . $max_height . '_' . $scale . '_' . filemtime($filename)) . '.png';
		if (!file_exists($cache_filename))
		{
			list($width, $height, $mime_type, $attribute) = getimagesize($filename);

			$new_width = 100;
			$new_height = 100;
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
			else if ($scale != 0)
			{
				$new_width = floor($scale * $width);
				$new_height = floor($scale * $height);
			}
			else
				return $filename;

			switch (image_type_to_mime_type($mime_type))
			{
				case 'image/jpeg':
					$image = imagecreatefromjpeg($filename);
					break;

				case 'image/png':
					$image = imagecreatefrompng($filename);
					imagealphablending($image, true);
					imagesavealpha($image, true);
					break;

				case 'image/gif':
					$image = imagecreatefromgif($filename);
					break;

				default:
					Log::warning('Could not resize "' . $filename . '" since it is not a JPEG, PNG or GIF');
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

	public static function checkModules()
	{
		global $db;

		$fs_modules = array();
		$handle = opendir('modules/');
		while (($module_name = readdir($handle)) !== false)
			if (is_dir('modules/' . $module_name) && $module_name != '.' && $module_name != '..')
			{
				$module_file = 'modules/' . $module_name . '/config.ini';
				if (file_exists($module_file) !== false)
					$fs_modules[$module_name] = 1;
			}

		// check with database
		$db_modules = $db->query("SELECT * FROM module;");
		while ($db_module = $db_modules->fetch())
			if (isset($fs_modules[$db_module['module_name']])) // file exists and the db entry too
				unset($fs_modules[$db_module['module_name']]);
			else // file does not exist but db entry does
			{
				Log::information('module with module_name "' . $db_module['module_name'] . '" doesn\'t exist in the filesystem and is removed from the database');

                // remove module table, link_module relations of the module, module entry
				$db->exec("
                DROP TABLE IF EXISTS module_" . $db->escape($db_module['module_name']) . ";
                DELETE FROM link_module WHERE module_name = '" . $db->escape($db_module['module_name']) . "';
                DELETE FROM module WHERE module_name = '" . $db->escape($db_module['module_name']) . "';");
			}

		foreach ($fs_modules as $name => $enabled) // file exists but db entry does not
		{
			include_once('modules/' . $name . '/admin/setup.php');

            Log::information('module with module_name "' . $name . '" is inserted into the database');
            $db->exec("INSERT INTO module (module_name, enabled) VALUES ('" . $db->escape($name) . "', 1);");
		}
	}

    public static function cleanDatabase()
    {
        global $db;

        // remove dead links, dead menu items
        $db->exec("
        DELETE FROM link WHERE NOT EXISTS (SELECT 1 FROM link_module WHERE link.link_id = link_module.link_id);
        DELETE FROM menu WHERE NOT EXISTS (SELECT 1 FROM link WHERE menu.link_id = link.link_id);");
    }
}

function minifyHtml($text)
{
    if (!Common::$minifying) {
        return $text;
    }

    $re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';
    $text = preg_replace($re, " ", $text);
    if ($text === null) exit("PCRE Error! File too big.\n");
    return $text;
}

?>
