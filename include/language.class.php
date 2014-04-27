<?php

function _($string)
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
	private static $language = null;

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
				self::$language = new Config('languages/' . $locale . '.conf');
		}
	}

	public static function translate($string, $parameters)
	{
		$translation = $string;
		if (self::$language !== null)
		{
			if (!self::$language->has($string))
				user_error('Translation for "' . $string . '" doesn\'t exist', NOTICE);
			else
				$translation = self::$language->get($string);
		}
		return vsprintf($translation, $parameters);
	}
}