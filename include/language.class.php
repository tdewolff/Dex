<?php

function __($string)
{
	$parameters = array();
	if (func_num_args() > 1)
	{
		$parameters = func_get_args();
		array_shift($parameters);
	}
	return Language::translate($string, $parameters);
}

class Language
{
	private static $language = array();

	public static function getAll()
	{
		$languages = array('en.English' => 'English');
		$handle = opendir('languages/');
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
		if ($locale != 'en.English')
		{
			if (!is_file('languages/' . $locale . '.conf'))
				user_error('Language file "languages/' . $locale . '.conf" doesn\'t exist', WARNING);
			else
				self::$language['core'] = new Config('languages/' . $locale . '.conf');
		}
	}

	public static function extend($domain, $root, $locale)
	{
		if ($locale != 'en.English')
			if (is_file($root . 'languages/' . $locale . '.conf'))
				self::$language[$domain] = new Config($root . 'languages/' . $locale . '.conf');
	}

	public static function translate($string, $parameters)
	{
		if (count(self::$language) > 0)
		{
			foreach (self::$language as $domain => $language)
				if ($language->has($string))
					return vsprintf($language->get($string), $parameters);
			user_error('Translation for "' . $string . '" doesn\'t exist', NOTICE);
		}
		return vsprintf($string, $parameters);
	}
}