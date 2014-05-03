<?php

class Config
{
	private $filename = '';
	private $lines = array();
	private $key_lines = array();

	private $pairs = array();
	private $defaults = array();

	public function __construct($filename)
	{
		$this->filename = $filename;
		if (!is_file($filename))
		{
			user_error('Could not find config file "' . $filename . '"', WARNING);
			return;
		}

		$file = preg_replace("/\r\n|\r/", "\n", trim(file_get_contents($filename)));
		if (!$file)
			return; // empty

		$this->lines = explode("\n", $file);
		foreach ($this->lines as $i => $line)
		{
			$line = trim($line);
			$semicolon_position = strpos($line, ';');
			if ($semicolon_position !== false)
				$line = substr($line, 0, $semicolon_position);

			$is_position = strpos($line, '=');
			if ($is_position === false)
				continue;

			$key = trim(substr($line, 0, $is_position));
			$value = trim(substr($line, $is_position + 1));
			$this->pairs[$key] = $value;
			$this->key_lines[$key] = $i;
		}
	}

	public function save()
	{
		if (!file_put_contents($this->filename, implode("\n", $this->lines)))
			user_error('Could not save config file "' . $this->filename . '"', ERROR);
	}

	public function getDefault($key)
	{
		return isset($this->defaults[$key]) ? $this->defaults[$key] : '';
	}

	public function setDefault($key, $value)
	{
		$this->defaults[$key] = $value;
	}

	public function has($key)
	{
		return isset($this->pairs[$key]);
	}

	public function get($key)
	{
		return isset($this->pairs[$key]) ? $this->pairs[$key] : (isset($this->defaults[$key]) ? $this->defaults[$key] : '');
	}

	public function set($key, $value)
	{
		$this->pairs[$key] = $value;
		if (isset($this->key_lines[$key]))
		{
			$this->lines[$this->key_lines[$key]] = $key . ' = ' . $value;
		}
		else
		{
			$this->lines[] = $key . ' = ' . $value;
			$this->key_lines[$key] = count($this->lines) - 1;
		}
	}
}