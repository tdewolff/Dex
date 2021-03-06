<?php

class Common
{
	public static $request_url = '';
	public static $base_url = '';

	////////////////

	private static $minifying = false;

	public static function setMinifying($minifying) {
		self::$minifying = $minifying;
	}

	public static function isMinifying() {
		return self::$minifying;
	}

	////////////////

	public static function requestResource()
	{
		return strpos(self::$request_url, 'res/') === 0;
	}

	public static function requestAdmin()
	{
		return strpos(self::$request_url, 'admin/') === 0 || !Db::isValid();
	}

	public static function requestAdminAuxiliary()
	{
		return strpos(self::$request_url, 'admin/auxiliary/') === 0;
	}

	public static function requestApi()
	{
		return strpos(self::$request_url, 'api/') === 0;
	}

	public static function requestAjax()
	{
		return (Common::requestApi() || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
	}

	////////////////

	public static function makeDirectory($directory)
	{
		if (!is_dir($directory))
			if (!mkdir($directory))
				user_error('Could not make directory "' . $directory . '"', WARNING);
	}

	public static function validUrl($input) // unused
	{
		// first part are all allowed characters
		// second part makes sure no ../ occurs
		// third part makes sure no more than one / is at the end
		return !preg_match('/([^a-zA-Z0-9\/\.\-_\?=& ]+)|(\.\.\/)|((.+)[\/]{2,}$)/', $input);
	}

	public static function hasMinExtension($filename)
	{
		$dot_position = strrpos($filename, '.');
		if ($dot_position !== false)
			return substr($filename, $dot_position - 4, 4) == '.min';
		return false;
	}

	public static function insertMinExtension($filename)
	{
		$dot_position = strrpos($filename, '.');
		if ($dot_position !== false)
			return substr($filename, 0, $dot_position) . '.min' . substr($filename, $dot_position);
		return $filename;
	}

	public static function formatBytes($size, $precision = 2)
	{
		if ($size > 0) {
			$base = log($size) / log(1000);
			$suffixes = array('', 'k', 'M', 'G', 'T');
			return round(pow(1000, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)] . 'B';
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

	public static function random($n)
	{
		$max = ceil($n / 40);
		$random = '';
		for ($i = 0; $i < $max; $i++) {
			$random .= sha1(microtime(true).mt_rand(10000, 90000));
		}
		return substr($random, 0, $n);
	}

	private static $column = '';
	public static function cmpOn($a, $b)
	{
		return strcmp($a[self::$column], $b[self::$column]);
	}

	public static function sortOn(&$array, $column)
	{
		self::$column = $column;
		usort($array, array('Common', 'cmpOn'));
	}

	public static function getDirectorySize($dir)
	{
		if (!is_readable($dir) || !($handle = @opendir($dir)))
			return false;

		$size = 0;
		while ($name = readdir($handle))
		{
			if (is_file($dir . $name) && is_readable($dir))
				$size += filesize($dir . $name);

			if (is_dir($dir . $name) && is_readable($dir) && $name != '.' && $name != '..')
				$size += self::getDirectorySize($dir . $name . '/');
		}
		closedir($handle);
		return $size;
	}

	////////////////

	public static function tryOrEmpty($array, $index)
	{
		return isset($array[$index]) ? $array[$index] : '';
	}

	public static function tryOrZero($array, $index)
	{
		return isset($array[$index]) ? $array[$index] : 0;
	}

	////////////////

	public static function getUrlContents($url)
	{
		$contents = null;
		$allow_url_fopen = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
		if ($allow_url_fopen)
		{
			try
			{
				$contents = file_get_contents($url, false, stream_context_create(array(
					'http' => array(
						'method' => 'GET',
						'max_redirects' => 0,
						'timeout' => 5,
					)
				)));
			}
			catch (Exception $e)
			{
				user_error('file_get_contents error: "' . $e->getMessages() . '"', WARNING);
			}
		}
		elseif (extension_loaded('curl'))
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$contents = curl_exec($ch);
			curl_close($ch);

			if ($contents === null)
				user_error('cURL error: "' . curl_error($ch) . '"', WARNING);
		}
		return $contents;
	}

	////////////////

	public static function fullBaseUrl()
	{
		$s = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
		return 'http' . $s . '://' . $_SERVER['HTTP_HOST'] . '/';
	}

	public static function responseCode($new_code = null)
	{
		static $code = 200;
		if ($new_code != null && !headers_sent())
		{
			header('X-PHP-Response-Code: ' . $new_code, true, $new_code);
			$code = $new_code;
		}
		return $code;
	}

	public static function outputFaviconIco()
	{
		if (is_file('favicon.ico'))
		{
			header('Content-Type: image/x-icon');
			echo file_get_contents('favicon.ico');
		}
		exit;
	}

	public static function outputRobotsTxt()
	{
		header('Content-Type: text');
		echo "User-agent: *\n" .
			 "Disallow: /admin/\n" .
			 "Sitemap: " . self::fullBaseUrl() . self::$base_url . "sitemap.xml";
		exit;
	}

	public static function outputSitemapXml()
	{
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>' .
			 '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$table = Db::query("SELECT link_id, url FROM link;");
		while ($row = $table->fetch())
		{
			$content = Db::singleQuery("SELECT modify_time FROM content WHERE link_id = '" . $row['link_id'] . "' ORDER BY modify_time DESC LIMIT 1;");
			if ($content)
			{
				$slashes = preg_match_all('/\//', $row['url'], $matches); // $matches not used but required for old PHP versions
				$priority = '0.5';
				if ($row['url'] == '')
					$priority = '1';
				else if ($slashes == 1)
					$priority = '0.8';
				else if ($slashes == 2)
					$priority = '0.6';

				echo '<url>' .
						 '<loc>' . self::fullBaseUrl() . self::$base_url . $row['url'] . '</loc>' .
						 '<lastmod>' . date('Y-m-d', $content['modify_time']) . '</lastmod>' .
						 '<priority>' . $priority . '</priority>' .
					 '</url>';
			}
		}
		echo '</urlset>';
		exit;
	}
}

ini_set('pcre.recursion_limit', '16777');
function minifyHtml($text)
{
	if (!Common::isMinifying())
		return $text;

	$text = trim(preg_replace('/(?<=>)\s+(?=<)/Ssi', '', $text)); // remove any whitespace between tags except one space

	$regex = '%# Collapse whitespace everywhere but in blacklisted elements.
		(?>             # Match all whitespans other than single space.
		  \s+           # or two or more consecutive-any-whitespace.
		)               # Note: The remaining regex consumes no text at all...
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
	$text = preg_replace($regex, ' ', $text);
	if ($text === null)
		user_error('Output HTML too large');

	// TODO: remove unneccessary quotes around HTML attributes NEEDS TO BE IMPROVED! Is this save?
	//$text = preg_replace('/(\s\w+=)"([^"\'`=<>\s]+)"/S', '\1\2', $text); // remove any whitespace between tags except one space

	// TODO: minify inline JS

	return $text;
}
