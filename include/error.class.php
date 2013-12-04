<?php

define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);
define('NOTICE', E_USER_NOTICE);

set_error_handler(array('Error', 'report'));
ini_set('display_errors', false);

class Error
{
	private static $display = false;
    private static $last = '';

	public static function setDisplay($display)
	{
		self::$display = $display;
		if (self::$display)
			ini_set('error_reporting', E_ALL | E_STRICT);
		else
			ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
	}

    public static function getLast()
    {
        return self::$last;
    }

	public static function report($type, $message, $file, $line, $context = '')
	{
        $display_message = self::$display ? $message : 'Error';
        self::$last = $display_message;

        if (Common::requestAjax() && !class_exists('API'))
            require_once('include/api.class.php');

		switch ($type)
		{
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
			case E_RECOVERABLE_ERROR:
				Log::warning('(' . $file . ':' . $line . ') ' . $message);
				if (self::$display && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::warning($message);
					else if (Common::requestAdmin())
						echo $message;
				}
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				Log::notice('(' . $file . ':' . $line . ') ' . $message);
				if (self::$display && !Common::requestResource())
				{
					if (Common::requestAjax())
						API::notice($message);
					else if (Common::requestAdmin())
						echo $message;
				}
				break;

            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            default:
                Log::error('(' . $file . ':' . $line . ') ' . $message);
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
                        echo $message;
                }
                exit;
		}
		return true;
	}
}

?>
