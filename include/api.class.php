<?php

class API
{
    private static $data = array();
    private static $response = array();

    ////////////////

    public static function load()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            parse_str(file_get_contents("php://input"), self::$data);
    }

    public static function action($action)
    {
        if (isset(self::$data['action']))
            return ($action == self::$data['action']);
        return false;
    }

    ////////////////

    public static function has($key)
    {
        return isset(self::$data[$key]);
    }

    public static function get($key)
    {
        return self::$data[$key];
    }

    public static function set($key, $value)
    {
        self::$response[$key] = $value;
    }

    ////////////////

    public static function error($message)
    {
        self::$response['error'][] = $message;
        self::finish();
    }

    public static function warning($message)
    {
        self::$response['error'][] = $message;
    }

    public static function notice($message)
    {
        self::$response['error'][] = $message;
    }

    ////////////////

    public static function finish()
    {
        if (!ob_get_length())
        {
            header('Content-type: application/json');
            echo json_encode(self::$response);
            exit;
        }
        print_r(self::$response);
        exit;
    }
}

?>