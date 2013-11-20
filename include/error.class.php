<?php

define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);
define('NOTICE', E_USER_NOTICE);

set_error_handler(array('Error', 'report'));
ini_set('display_errors', false);

class Error
{
	private static $display = false;

	public static function setDisplay($display)
	{
		self::$display = $display;
		if (self::$display)
			ini_set('error_reporting', E_ALL | E_STRICT);
		else
			ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
	}

	public static function report($type, $error, $file, $line, $context = '')
	{
		switch ($type)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				Log::error('(' . $file . ':' . $line . ') ' . $error);
				if (self::$display && !Common::requestResource())
					if (Common::requestApi())
						API::error('<pre><b>ERROR</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>');
					else
						echo '<pre><b>ERROR</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>';
				exit;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
			case E_RECOVERABLE_ERROR:
				Log::warning('(' . $file . ':' . $line . ') ' . $error);
				if (self::$display && !Common::requestResource())
					if (Common::requestApi())
						API::warning('<pre><b>WARNING</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>');
					else
						echo '<pre><b>WARNING</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>';
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				Log::notice('(' . $file . ':' . $line . ') ' . $error);
				if (self::$display && !Common::requestResource())
					if (Common::requestApi())
						API::notice('<pre><b>NOTICE</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>');
					else
						echo '<pre><b>NOTICE</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>';
				break;

			default:
				Log::error('(' . $file . ':' . $line . ') ' . $error);
				if (self::$display && !Common::requestResource())
					if (Common::requestApi())
						API::error('<pre><b>UNKNOWN (' . $type . ')</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>');
					else
						echo '<pre><b>UNKNOWN (' . $type . ')</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '</pre>';
				exit;
		}
		return true;
	}
}

?>
