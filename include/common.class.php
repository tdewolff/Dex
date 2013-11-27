<?php

class Common
{
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
        global $request_url;
        return strpos($request_url, 'res/') === 0;
    }

    public static function requestAdmin()
    {
        global $request_url;
        return strpos($request_url, 'admin/') === 0;
    }

    public static function requestApi()
    {
        global $request_url;
        return strpos($request_url, 'api/') === 0;
    }

    ////////////////

    public static function ensureWritableDirectory($directory)
    {
        if (!is_dir($directory))
            mkdir($directory, 0755);
        else if (substr(sprintf('%o', fileperms($directory)), -4) !== '0755')
            chmod($directory, 0755);
    }

	public static function validUrl($input)
	{
		// first part are all allowed characters
		// second part makes sure no ../ occurs
		// third part makes sure no more than one / is a t the end
		return !preg_match('/([^a-zA-Z0-9\/\.\-_\?=& ]+)|(\.\.\/)|((.+)[\/]{2,}$)/', $input);
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

    public static function sortOn($array, $column)
    {
        self::$column = $column;
        usort($array, array('Common', 'cmpOn'));
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

    public static function tryOrDefault($array, $index, $default)
    {
        return isset($array[$index]) ? $array[$index] : $default;
    }

    ////////////////

    public static function fullBaseUrl()
    {
        $s = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
        return 'http' . $s . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    public static function outputFaviconIco()
    {
        if (file_exists('favicon.ico'))
        {
            header('Content-Type: image/x-icon');
            echo file_get_contents('favicon.ico');
        }
        exit;
    }

    public static function outputRobotsTxt()
    {
        global $base_url;

        header('Content-Type: text');
        echo "User-agent: *\n" .
             "Disallow: /admin/\n" .
             "Sitemap: " . self::fullBaseUrl() . $base_url . "sitemap.xml";
        exit;
    }

    public static function outputSitemapXml()
    {
        global $db, $base_url;

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' .
             '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $table = $db->query("SELECT * FROM link;");
        while ($row = $table->fetch())
        {
            echo '<url>' .
                 '<loc>' . self::fullBaseUrl() . $base_url . $row['url'] . '</loc>' .
                 '</url>';
        }
        echo '</urlset>';
        exit;
    }
}

function minifyHtml($text)
{
    if (!Common::isMinifying()) {
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
