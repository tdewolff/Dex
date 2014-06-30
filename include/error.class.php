<?php

define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);
define('NOTICE', E_USER_NOTICE);

set_error_handler(array('Error', 'report'));
ini_set('display_errors', false);

class Error
{
	private static $display_errors = false;
	private static $display_notices = false;
	private static $messages = array();

	public static function setDisplay($display_errors, $display_notices)
	{
		self::$display_errors = $display_errors;
		self::$display_notices = $display_notices;
		if (self::$display_errors)
			ini_set('error_reporting', E_ALL | E_STRICT);
		else
			ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
	}

	public static function getErrors()
	{
		if (!self::$display_errors)
			return '<p class="error">' . (function_exists('__') ? __('A server error occurred') : 'A server error occurred') . '</p>';
		return implode('', self::$messages);
	}

	public static function stripBacktrace($backtrace)
	{
		if (!is_array($backtrace))
			return '';

		$stripped_backtrace = array();
		foreach ($backtrace as $row)
			$stripped_backtrace[] = array(
				'file' => (isset($row['file']) ? $row['file'] : '-') . (isset($row['line']) ? ':' . $row['line'] : ''),
				'function' => (isset($row['class']) ? $row['class'] : '') . (isset($row['type']) ? $row['type'] : '') . (isset($row['function']) ? $row['function'] : '-')
			);
		return $stripped_backtrace;
	}

	public static function formatError($message, $location, $backtrace)
	{
		$formatted_message = '<p class="error"><strong>' . $message . '</strong>' . ($location !== false ? '<br><small>' . $location . '</small>' : '') . '</p>';
		if (is_array($backtrace) && count($backtrace))
		{
			$formatted_message .= '<table class="backtrace"><thead><tr><th>File</th><th>Function</th></tr></thead><tbody>';
			foreach ($backtrace as $row)
				$formatted_message .= '<tr><td>' . (isset($row['file']) ? $row['file'] : '') . '</td><td>' . (isset($row['function']) ? $row['function'] : '') . '</td></tr>';
			$formatted_message .= '</tbody></table>';
		}
		return $formatted_message;
	}

	public static function report($type, $message, $file, $line)
	{
		if (error_reporting() === 0) // ignore error when prepended with @
			return true;

		$location = ($file && $line ? $file . ':' . $line : '');
		$backtrace = self::stripBacktrace(debug_backtrace());
		$formatted_message = self::formatError($message, $location, $backtrace);
		self::$messages[] = $formatted_message;

		if (Common::requestAjax() && !class_exists('API'))
			require_once(dirname($_SERVER['SCRIPT_FILENAME']) . '/include/api.class.php');

		switch ($type)
		{
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
			case E_RECOVERABLE_ERROR:
				Log::warning($message, $location, $backtrace);
				if (self::$display_notices && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::warning($message, $formatted_message);
					else if (Common::requestAdmin())
						echo $formatted_message;
				}
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				Log::notice($message, $location, $backtrace);
				if (self::$display_notices && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::notice($message, $formatted_message);
					else if (Common::requestAdmin())
						echo $formatted_message;
				}
				break;

			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			default:
				Log::error($message, $location, $backtrace);
				if (Common::responseCode() == 200)
					Common::responseCode(500);

				if (Common::requestResource())
					header('Content-Type: text/html; charset: UTF-8');

				$display_message = self::$display_errors ? $message : (function_exists('__') ? __('A server error occurred') : 'A server error occurred');
				$display_formatted_message = self::$display_errors ? $formatted_message : '<p class="error">' . $display_message . '</p>';
				if (Common::requestAjax())
					API::error($display_message, $display_formatted_message);
				else if (class_exists('Hooks'))
				{
					if (Common::requestAdmin())
						Hooks::emit('admin-error');
					else
						Hooks::emit('site-error');
				}
				else
					echo $display_formatted_message;
				exit;
		}
		return true;
	}
}
