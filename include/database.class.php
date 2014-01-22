<?php

if (get_magic_quotes_gpc()) {
    function strip_array($var) {
        return is_array($var) ? array_map("strip_array", $var) : stripslashes($var);
    }

    $_POST = strip_array($_POST);
    $_SESSION = strip_array($_SESSION);
    $_GET = strip_array($_GET);
}

function _sqliteRegexp($pattern, $string) {
	$pattern = preg_replace('/\//', '\/', $pattern);
	return preg_match('/^' . $pattern . '$/', $string);
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
	}

	public function __destruct()
	{
		Log::notice('Database close: ' . $this->filename);
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

	public function lastId()
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
