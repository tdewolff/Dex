<?php

class Hooks
{
	private static $hooks = array();

	public static function preAttach($event, $callback)
	{
		if (!isset(self::$hooks[$event]))
			self::$hooks[$event] = array();

		array_unshift(self::$hooks[$event], $callback);
	}

	public static function attach($event, $callback)
	{
		if (!isset(self::$hooks[$event]))
			self::$hooks[$event] = array();

		self::$hooks[$event][] = $callback;
	}

	public static function emit($event, &$arg0 = null, &$arg1 = null, &$arg2 = null, &$arg3 = null, &$arg4 = null, &$arg5 = null, &$arg6 = null, &$arg7 = null)
	{
		$args = array();
		$argc = func_num_args();
		for ($i = 0; $i < $argc; $i++)
		{
			$name = 'arg' . $i;
			$args[] = & $$name;
		}

		if (isset(self::$hooks[$event]))
			foreach (self::$hooks[$event] as $callback)
				call_user_func_array($callback, $args);
	}

	public static function clear($event)
	{
		if (isset(self::$hooks[$event]))
			self::$hooks[$event] = array();
	}
}

?>
