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
		$all = preg_replace("/\r\n|\r/", "\n", trim(file_get_contents(self::$filename)));
		if (!$all)
			return array();
		return explode("\n", $all);
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
			if ($read_size > 0)
				$input = preg_replace("/\r\n|\r/", "\n", fread($fp, $read_size), -1, $line_count) . $input;
			//$line_count = substr_count(ltrim($input), "\n");

			// if $pos is == 0 we are at start of file
			$pos -= $read_size;
			if (!$pos)
				break;
		}
		fclose($fp);
		return array_slice(explode("\n", rtrim($input)), -$n);
	}

	public static function getLoglineDetails($logline)
	{
		$details = array('message' => '', 'location' => '', 'backtrace' => '');

		if (strlen($logline) == 0)
			return false;

		$backtrace_pos = strpos($logline, '[', 1);
		if ($backtrace_pos !== false)
		{
			$details['backtrace'] = json_decode(substr($logline, $backtrace_pos), true);
			$logline = substr($logline, 0, $backtrace_pos - 1);
		}

		$location_pos = strrpos($logline, '(');
		if ($location_pos !== false)
		{
			$details['location'] = preg_replace(array('/&lpar;/', '/&rpar;/'), array('(', ')'), substr($logline, $location_pos));
			$logline = substr($logline, 0, $location_pos - 1);
		}

		$logline_array = explode(' ', $logline);
		if (count($logline_array) < 4)
			return false;

		$details['datetime'] = substr($logline_array[0], 1) . ' ' . substr($logline_array[1], 0, -1);
		$details['ipaddress'] = $logline_array[2];
		$details['type'] = $logline_array[3];

		$message = implode(' ', array_slice($logline_array, 3));
		if (strlen($message) > 8)
			$details['message'] = preg_replace(array('/&lpar;/', '/&rpar;/', '/&lbrack;/', '/&rbrack;/'), array('(', ')', '[', ']'), substr($message, 8));

		return $details;
	}

	public static function error($message, $location = '', $backtrace = '') {
		self::appendLog('ERROR  ', $message, $location, $backtrace);
	}

	public static function warning($message, $location = '', $backtrace = '') {
		self::appendLog('WARNING', $message, $location, $backtrace);
	}

	public static function notice($message, $location = '', $backtrace = '') {
		if (self::$verbose)
			self::appendLog('NOTICE ', $message, $location, $backtrace);
	}

	public static function request($message, $location = '', $backtrace = '') {
		if (self::$verbose)
			self::appendLog('REQUEST', $message, $location, $backtrace);
	}

	public static function caching($message, $location = '', $backtrace = '') {
		if (self::$verbose)
			self::appendLog('CACHING', $message, $location, $backtrace);
	}

	private static function appendLog($type, $message, $location, $backtrace)
	{
		if (self::$file)
		{
			$message = preg_replace(array('/\(/', '/\)/', '/\[/', '/\]/', "/\r\n|\r|\n/"), array('&lpar;', '&rpar;', '&lbrack;', '&rbrack;', ' '), $message);
			if (strlen($location))
				$location = ' (' . preg_replace(array('/\(/', '/\)/'), array('&lpar;', '&rpar;'), $location) . ')';

			$message = '[' . date('Y-m-d H:i:s') . '] ' . self::$ipaddress . ' ' . $type . ' ' . $message . $location . (is_array($backtrace) ? ' ' . json_encode($backtrace) : '') . "\r\n";

			// try a few times to acquire file lock, if we don't lock simultaneous write might occur!
			for ($i = 0; $i < 10; $i++)
			{
				if (flock(self::$file, LOCK_EX))
				{
					fwrite(self::$file, $message);
					fflush(self::$file);
					flock(self::$file, LOCK_UN);
					break;
				}
				usleep(1);
			}
		}
	}
}
