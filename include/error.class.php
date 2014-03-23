<?php

define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);
define('NOTICE', E_USER_NOTICE);

set_error_handler(array('Error', 'report'));
ini_set('display_errors', false);

class Error
{
	private static $display = false;
	private static $messages = array();

	public static function setDisplay($display)
	{
		self::$display = $display;
		if (self::$display)
			ini_set('error_reporting', E_ALL | E_STRICT);
		else
			ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
	}

	public static function getErrors()
	{
		if (!self::$display)
			return '<span class="error">An error occurred, check the logs for additional information</span>';
		return implode('', self::$messages) . '!';
	}

	public static function stripBacktrace($backtrace)
	{
		if (!is_array($backtrace))
			return '';

		$stripped_backtrace = array();
		foreach ($backtrace as $row)
			$stripped_backtrace[] = array(
				'file' => (isset($row['file']) ? $row['file'] : '') . (isset($row['line']) ? ':' . $row['line'] : ''),
				'function' => (isset($row['class']) ? $row['class'] : '') . (isset($row['type']) ? $row['type'] : '') . (isset($row['function']) ? $row['function'] : '')
			);
		return $stripped_backtrace;
	}

	public static function formatError($message, $location, $backtrace)
	{
		$formatted_message = '<span class="error"><strong>' . $message . '</strong></span><span class="error-source">' . $location . '</span>';

		$formatted_backtrace = '';
		if (is_array($backtrace))
		{
			$formatted_backtrace = '<table class="backtrace"><thead><tr><th>File</th><th>Function</th></tr></thead><tbody>';
			foreach ($backtrace as $row)
				$formatted_backtrace .= '<tr><td>' . (isset($row['file']) ? $row['file'] : '') . '</td><td>' . (isset($row['function']) ? $row['function'] : '') . '</td></tr>';
		}
		$formatted_backtrace .= '</tbody></table>';

		return $formatted_message . $formatted_backtrace;
	}

	public static function report($type, $message, $file, $line)
	{
		$location = ($file && $line ? $file . ':' . $line : '');
		$backtrace = self::stripBacktrace(debug_backtrace());
		$formatted_message = self::formatError($message, $location, $backtrace);

		$display_message = self::$display ? $formatted_message : '<span class="error">An error occurred, check the logs for additional information</span>';
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
				if (self::$display && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::warning($message);
					else if (Common::requestAdmin())
						echo $formatted_message . (strlen($location) ? ' (' . $location . ')' : '');
				}
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				Log::notice($message, $location, $backtrace);
				if (self::$display && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::notice($message);
					else if (Common::requestAdmin())
						echo $formatted_message . (strlen($location) ? ' (' . $location . ')' : '');
				}
				break;

			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			default:
				Log::error($message, $location, $backtrace);
				if (!Common::requestResource())
				{
					if (Common::requestAjax())
						API::error($display_message);
					else if (class_exists('Hooks'))
					{
						if (Common::requestAdmin())
							Hooks::emit('admin-error');
						else
							Hooks::emit('error');
					}
					else
						echo $display_message;
				}
				exit;
		}
		return true;
	}
}

?>
