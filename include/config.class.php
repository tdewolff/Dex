<?php

class Config
{
	private $pairs = array();

	public function __construct($filename)
	{
		if (!is_file($filename))
		{
			user_error('Could not find config file "' . $filename . '"', WARNING);
			return;
		}

		$file = preg_replace("/\r\n|\r/", "\n", trim(file_get_contents($filename)));
		if (!$file)
			return; // empty

		$lines = explode("\n", $file);
		foreach ($lines as $i => $line)
		{
			$line = trim($line);
			$is_position = strpos($line, '=');
			if ($is_position === false || $line[0] == ';')
				continue;

			$key = trim(substr($line, 0, $is_position));
			$value = trim(substr($line, $is_position + 1));
			$this->pairs[$key] = $value;
		}
	}

	public function setDefault($key, $default)
	{
		if (!isset($this->pairs[$key]))
			$this->pairs[$key] = $default;
	}

	public function has($key)
	{
		return isset($this->pairs[$key]);
	}

	public function get($key)
	{
		return isset($this->pairs[$key]) ? $this->pairs[$key] : '';
	}
}