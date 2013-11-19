<?php

define('QUIET', 0);
define('VERBOSE', 1);

class Log
{
	private static $file = false;
	private static $level = VERBOSE;
	private static $filename = '';
	private static $ipaddress = '';

	function __destruct()
	{
		self::close();
	}

	public static function initialize($level, $directory)
	{
		if ($directory[strlen($directory) - 1] != '/')
			$directory .= '/';

		// absolute path needed for atexit()
		self::$filename = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/")) . '/' . $directory . self::getFilename();
		self::$level = $level;
		self::$ipaddress = Common::padIpAddress($_SERVER['REMOTE_ADDR']);

		self::open();
	}

	public static function open()
	{
		self::$file = @fopen(self::$filename, 'a');
	}

	public static function close()
	{
		if (self::$file)
			fclose(self::$file);
	}

	public static function getFilename()
	{
		return date('Y-m M') . '.log';
	}

	public static function isVerbose()
	{
		return self::$level == VERBOSE;
	}

	public static function error($message) {
		self::entry('ERROR  ', $message);
	}

	public static function warning($message) {
		self::entry('WARNING', $message);
	}

	public static function notice($message) {
		if (self::isVerbose())
			self::entry('NOTICE ', $message);
	}

	public static function request($message) {
		if (self::isVerbose())
			self::entry('REQUEST', $message);
	}

	public static function caching($message) {
		if (self::isVerbose())
			self::entry('CACHING', $message);
	}

	private static function entry($type, $message)
	{
		if (self::$file)
		{
			$message = '[' . date('Y-m-d H:i:s') . '] ' . self::$ipaddress . ' ' . $type . ' ' . $message . "\r\n";
		    fwrite(self::$file, $message);
		    fflush(self::$file);
		}
	}
}

?>
