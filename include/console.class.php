<?php

class Console
{
	private static $console = false;

	public static function hasOutput()
	{
		return is_file('console.txt');
	}

	public static function getOutput()
	{
		return file_get_contents('console.txt');
	}

	public static function append($message)
	{
		if (!self::$console)
			self::$console = fopen('console.txt', 'w');

		fwrite(self::$console, $message);
		fflush(self::$console);
	}

	public static function appendLine($message)
	{
		self::append($message . "\n");
	}

	public static function finish()
	{
		if (self::$console)
		{
			fclose(self::$console);
			sleep(1); // give time to read out console for the client
			unlink('console.txt');
		}
	}
}

?>