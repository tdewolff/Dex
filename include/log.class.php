<?php

define('VERBOSE', 1);
define('QUIET', 0);

class Log
{
	private static $filename = '';
	private static $verbose = true;

	private function __constructor()
	{
	}

	public static function setLevel($level = VERBOSE)
	{
		if ($level == VERBOSE)
			self::$verbose = true;
		else
			self::$verbose = false;
	}

	public static function setDirectory($directory)
	{
		if ($directory[strlen($directory) - 1] != '/')
			$directory .= '/';

		// absolute path needed for atexit()
		self::$filename = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/")) . '/' . $directory . self::getCurrentFilename();
	}

	public static function getCurrentFilename()
	{
		return date('Y-m M') . '.log';
	}

	public static function error($message) {
		self::entry('ERROR !', $message);
	}

	public static function warning($message) {
		self::entry('WARNING', $message);
	}

	public static function request($message) {
		if (self::$verbose)
			self::entry('REQUEST', $message);
	}

	public static function information($message) {
		if (self::$verbose)
			self::entry('INFO   ', $message);
	}

	public static function caching($message) {
		if (self::$verbose)
			self::entry('CACHING', $message);
	}

	private static function entry($type, $message)
	{
		$message = '[' . date('Y-m-d H:i:s') . '] ' . Common::padIpAddress($_SERVER['REMOTE_ADDR']) . ' ' . $type . ' ' . $message . "\r\n";

		if (file_exists($message))
		{
			$f = fopen(self::$filename, 'a');
		    fwrite($f, $message);
		    fclose($f);
		}
	}
}

?>
