<?php

function _sqliteRegexp($pattern, $string)
{
	$pattern = preg_replace('/\//', '\/', $pattern);
	return preg_match('/^' . $pattern . '$/', $string);
}

function _sqliteJsonAttr($json, $key)
{
	$json = json_decode($json, true);
	if (isset($json[$key]))
		return $json[$key];
	return null;
}

class Database
{
	public $filename = '';
	private $handle = null;
	private $queries = 0;

	public function __construct($filename)
	{
		$this->filename = $filename;
		$this->handle = new SQLite3($this->filename);
		$this->handle->createFunction('REGEXP', '_sqliteRegexp', 2);
		$this->handle->createFunction('JSONATTR', '_sqliteJsonAttr', 2);
	}

	public function __destruct()
	{
		$this->handle->close();
	}

	public function exec($sql)
	{
		$this->queries++;
		$this->handle->exec($sql);
	}

	public function query($sql)
	{
		$this->queries += 2; // SQLite seems to do two queries in this case .. !
		return new Result($this->handle->query($sql));
	}

	public function querySingle($sql)
	{
		$this->queries++;
		return $this->handle->querySingle($sql, true);
	}

	public function escape($value)
	{
		return $this->handle->escapeString($value);
	}

	public function queries()
	{
		return $this->queries;
	}

	public function last_id()
	{
		return $this->handle->lastInsertRowID();
	}

}

class Result
{
	private $result = null;

	public function __construct($result)
	{
		$this->result = $result;
	}

	public function fetch($key = '')
	{
		if (!strlen($key))
		{
			return $this->result->fetchArray(SQLITE3_ASSOC);
		}

		$ret = $this->result->fetchArray(SQLITE3_ASSOC);
		return ($ret && isset($ret[$key]) ? $ret[$key] : false);
	}
}

?>
