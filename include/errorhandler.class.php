<?php

define('EXPLICIT', 1);
define('DISCREET', 0);

define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);

class ErrorHandler
{
	private static $level = 0;

	private function __constructor() {}

	public static function initialize($level = DISCREET)
	{
		set_error_handler(array('ErrorHandler', 'report'));

		self::$level = $level;
		if (self::$level == DISCREET)
		{
			ini_set('error_reporting', E_ALL | E_STRICT);
			ini_set('display_errors', false);
			ini_set('log_errors', false);
		}
		else
			ini_set('error_reporting', E_ALL | ~E_NOTICE);
	}

	public static function getLevel()
	{
		return self::$level;
	}

	public static function report($type, $error, $file, $line, $context)
	{
		switch ($type)
		{
			case ERROR:
				Log::error('(' . $file . ':' . $line . ') ' . $error);
				if (self::$level == EXPLICIT)
					echo '<pre><b>ERROR</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '<br></pre>';
				exit;

			case WARNING:
				Log::warning('(' . $file . ':' . $line . ') ' . $error);
				if (self::$level == EXPLICIT)
					echo '<pre><b>WARNING</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '<br></pre>';
				break;

			default:
				Log::error('(' . $file . ':' . $line . ') ' . $error);
				if (self::$level == EXPLICIT)
					echo '<pre><b>UNKNOWN</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '<br></pre>';
				exit;
		}
		return true;
	}
}

?>
