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
		self::$filename = Common::$base_path . self::getFilename();
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

	public static function getLastLines($n, $errors_only)
	{
		$buffer_size = 1024;

		if (!($fp = fopen(self::$filename, 'r')))
			return array();
		fseek($fp, 0, SEEK_END);
		$size = ftell($fp);

		$pos = -min($buffer_size, $size);
		$carry = array();

		$lines = array();
		while ($n > 0) {
			if (fseek($fp, $pos, SEEK_END))
				break;

			$buffer = fread($fp, $buffer_size);
			$buffer_lines = explode("\n", $buffer);
			if (count($carry))
				$buffer_lines[count($buffer_lines) - 1] .= $carry;
			$carry = array_shift($buffer_lines);

			$read_lines = array();
			for ($i = 0; $i < count($buffer_lines); $i++)
			{
				$line = self::getLoglineDetails($buffer_lines[$i]);
				if ($line && (!$errors_only || $line['type'] == 'ERROR' || $line['type'] == 'WARNING'))
					$read_lines[] = $line;
			}

			if (count($read_lines) >= $n) // read too much
			{
				$lines = array_merge(array_slice($read_lines, count($read_lines) - $n), $lines);
				break;
			}
			else if (-$pos >= $size) // arrived at file start
			{
				$lines = array_merge($carry, $read_lines, $lines);
				break;
			}
			else // continue
			{
				$lines = array_merge($read_lines, $lines);
				$n -= count($read_lines);

				$pos -= $buffer_size;
				$pos = max($pos, -$size);
			}
		}
		fclose($fp);
		return $lines;
	}

	private static function getLoglineDetails($logline)
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
