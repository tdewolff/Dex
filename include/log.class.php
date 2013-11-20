<?php

class Log
{
	private static $file = false;
	private static $verbose = true;
	private static $directory = '';
	private static $filename = '';
	private static $ipaddress = '';

	function __destruct()
	{
		self::close();
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

	public static function setDirectory($directory)
	{
		if ($directory[strlen($directory) - 1] != '/')
			$directory .= '/';
		Common::ensureWritableDirectory($directory);
		self::$directory = $directory;

		// absolute path needed for atexit()
		self::$filename = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')) . '/' . self::getFilename();
		self::$ipaddress = Common::padIpAddress($_SERVER['REMOTE_ADDR']);

		self::open();
	}

	public static function setVerbose($verbose)
	{
		self::$verbose = $verbose;
	}

	public static function getFilename()
	{
		return self::$directory . date('Y-m M') . '.log';
	}

	public static function error($message) {
		self::entry('ERROR  ', $message);
	}

	public static function warning($message) {
		self::entry('WARNING', $message);
	}

	public static function notice($message) {
		if (self::$verbose)
			self::entry('NOTICE ', $message);
	}

	public static function request($message) {
		if (self::$verbose)
			self::entry('REQUEST', $message);
	}

	public static function caching($message) {
		if (self::$verbose)
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
