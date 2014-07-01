<?php

function __($string)
{
	$domain = 'core';
	$base_path = substr(str_replace('\\', '/', __FILE__), 0, -strlen('include/language.class.php'));

	$trace = debug_backtrace();
	while (count($trace))
	{
		$caller = array_shift($trace);
		$file = str_replace('\\', '/', $caller['file']);
		$filepath = explode('/', substr($file, strlen($base_path)));
		if (count($filepath) > 1 && ($filepath[0] == 'core' || $filepath[0] == 'modules' || $filepath[0] == 'templates' || $filepath[0] == 'themes'))
		{
			if ($filepath[0] != 'core')
				$domain = $filepath[0] . '_' . $filepath[1];
			break;
		}
	}

	$parameters = array();
	if (func_num_args() > 1)
	{
		$parameters = func_get_args();
		array_shift($parameters);
	}
	return Language::translate($domain, $string, $parameters);
}

function ___($domain, $string)
{
	$parameters = array();
	if (func_num_args() > 2)
	{
		$parameters = func_get_args();
		array_shift($parameters);
		array_shift($parameters);
	}
	return Language::translate($domain, $string, $parameters);
}

class Language
{
	private static $language = array();

	public static function getAll()
	{
		$languages = array('en.English' => 'English');
		if (($handle = opendir('languages/')) !== false)
			while (($name = readdir($handle)) !== false)
				if (is_file('languages/' . $name) && substr($name, -5) == '.conf')
				{
					$dot_position = strpos($name, '.');
					$languages[substr($name, 0, -5)] = substr($name, $dot_position + 1, -5);
				}

		asort($languages);
		return $languages;
	}

	public static function load($locale)
	{
		if ($locale != '' && $locale != 'en.English')
		{
			if (!is_file('languages/' . $locale . '.conf'))
				user_error('Language file "languages/' . $locale . '.conf" doesn\'t exist', WARNING);
			else
				self::$language['core'] = new Config('languages/' . $locale . '.conf');
		}
	}

	public static function extend($domain, $name, $locale)
	{
		if ($locale != '' && $locale != 'en.English' && !isset(self::$language[$domain . '_' . $name]))
			if (is_file($domain . '/' . $name . '/languages/' . $locale . '.conf'))
				self::$language[$domain . '_' . $name] = new Config($domain . '/' . $name . '/languages/' . $locale . '.conf');
	}

	public static function translate($domain, $string, $parameters)
	{
		if (count(self::$language) > 0)
		{
			if (isset(self::$language[$domain]) && self::$language[$domain]->has($string))
				return vsprintf(self::$language[$domain]->get($string), $parameters);
			if ($domain != 'core' && isset(self::$language['core']) && self::$language['core']->has($string))
				return vsprintf(self::$language['core']->get($string), $parameters);
			user_error('Translation for "' . $string . '" in domain "' . $domain . '" doesn\'t exist', NOTICE);
		}
		return vsprintf($string, $parameters);
	}
}