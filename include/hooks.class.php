<?php

function sortEvent($a, $b)
{
	return strnatcmp($a['order'], $b['order']);
}

class Hooks
{
	private static $hooks = array();

	public static function attach($event, $order, $callback)
	{
		if (!isset(self::$hooks[$event]))
			self::$hooks[$event] = array();

		self::$hooks[$event][] = array('order' => $order, 'callback' => $callback);
		uasort(self::$hooks[$event], 'sortEvent');
	}

	public static function emit($event)
	{
		if (isset(self::$hooks[$event]))
			foreach (self::$hooks[$event] as $item)
				call_user_func($item['callback']);
	}

	public static function clear($event)
	{
		if (isset(self::$hooks[$event]))
			self::$hooks[$event] = array();
	}
}

?>
