<?php

class Log
{
	private static $file = false;
	private static $verbose = true;
	private static $filename = '';
	private static $ipaddress = '';

	function __destruct()
	{
		self::close();
	}

	public static function initialize()
	{
		// absolute path needed for register_shutdown_function()
		self::$filename = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . self::getFilename();
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

	public static function setVerbose($verbose)
	{
		self::$verbose = $verbose;
	}

	public static function getFilename()
	{
		return 'logs/' . date('Y-m M') . '.log';
	}

	public static function getAllLines()
	{
		$all = trim(file_get_contents(self::$filename));
		if (!$all)
	    	return array();
	    return explode("\r\n", $all);
	}

	public static function getLastLines($n)
	{
	    $buffer_size = 1024;

	    if (!($fp = fopen(self::$filename, 'r')))
	        return array();

	    fseek($fp, 0, SEEK_END);
	    $pos = ftell($fp);

	    $input = '';
	    $line_count = 0;
	    while ($line_count < $n + 1)
	    {
	        // read the previous block of input
	        $read_size = $pos >= $buffer_size ? $buffer_size : $pos;
	        fseek($fp, $pos - $read_size, SEEK_SET);

	        // prepend the current block, and count the new lines
	        $input = fread($fp, $read_size).$input;
	        $line_count = substr_count(ltrim($input), "\r\n");

	        // if $pos is == 0 we are at start of file
	        $pos -= $read_size;
	        if (!$pos)
	            break;
	    }
	    fclose($fp);
	    return array_slice(explode("\r\n", rtrim($input)), -$n);
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
