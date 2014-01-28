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

class Db
{
    private static $filename = '';
    private static $handle = null;
    private static $queries = 0;

    public static function open($filename)
    {
        if (self::$handle)
            self::close();

        self::$filename = $filename;
        self::$handle = new SQLite3($filename);
        self::$handle->createFunction('REGEXP', '_sqliteRegexp', 2);

        if (is_file($filename) === false)
            user_error('Database file never created at "' . $filename . '"', ERROR);
    }

    public static function close()
    {
        self::$handle->close();
        self::$handle = null;
    }

    public static function unlink()
    {
        self::close();
        unlink(self::$filename);
    }

    public static function isValid()
    {
        return self::$handle && is_file(self::$filename) && filesize(self::$filename) !== 0;
    }

    public static function exec($sql)
    {
        self::$queries++;
        return self::$handle->exec($sql);
    }

    public static function query($sql)
    {
        self::$queries += 2; // SQLite seems to do two queries in this case .. !
        return new Result(self::$handle->query($sql));
    }

    public static function querySingle($sql)
    {
        self::$queries++;
        return self::$handle->querySingle($sql, true);
    }

    public static function escape($value)
    {
        return self::$handle->escapeString($value);
    }

    public static function queries()
    {
        return self::$queries;
    }

    public static function lastId()
    {
        return self::$handle->lastInsertRowID();
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

    public function fetchAll($key = '')
    {
        $result = Array();
        while ($result[] = $this->fetch($key));
        array_pop($result);
        return $result;
    }
}

?>
