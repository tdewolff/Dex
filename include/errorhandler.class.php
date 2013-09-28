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
		if (self::$level == EXPLICIT)
			ini_set('error_reporting', E_ALL);
	}

	public static function getLevel()
	{
		return self::$level;
	}

	public static function report($type, $error, $file, $line)
	{
		switch ($type)
		{
			case ERROR:
				Log::error('(' . $file . ':' . $line . ') ' . $error);
				if (self::$level == EXPLICIT)
					echo '<pre><b>ERROR</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '<br /></pre>';
				Hooks::emit('error');
				exit;

			case WARNING:
				Log::warning('(' . $file . ':' . $line . ') ' . $error);
				if (self::$level == EXPLICIT)
					echo '<pre><b>WARNING</b> ' . htmlentities('(' . $file . ':' . $line . ') ' . $error) . '<br /></pre>';
				break;

			default:
				return false;
		}
		return true;
	}
}

?>
