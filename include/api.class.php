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

	public static function expandUrl($url)
	{
		$filename = '';
		if (isset($url[1]) && isset($url[2]))
			if ($url[1] == 'core')
				$filename = 'core/api/' . implode('/', array_splice($url, 2)) . '.php';
			else if ($url[1] == 'module')
				$filename = 'modules/' . $url[2] . '/api/' . implode('/', array_splice($url, 3)) . '.php';
			else if ($url[1] == 'template')
				$filename = 'templates/' . $url[2] . '/api/' . implode('/', array_splice($url, 3)) . '.php';

		if (empty($filename))
		{
			Common::responseCode(404);
			user_error('Could not expand URL "' . implode('/', $url) . '" to API', ERROR);
		}

		return $filename;
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

	public static function error($message, $formatted_message)
	{
		self::$response['error'][] = $message;
		self::$response['formatted_error'][] = $formatted_message;
		self::finish();
	}

	public static function warning($message, $formatted_message)
	{
		self::$response['error'][] = $message;
		self::$response['formatted_error'][] = $formatted_message;
	}

	public static function notice($message, $formatted_message)
	{
		self::$response['error'][] = $message;
		self::$response['formatted_error'][] = $formatted_message;
	}

	////////////////

	public static function finish()
	{
		if (!ob_get_length())
		{
			header('Content-type: application/json');
			echo json_encode(self::$response);
		}
		exit;
	}
}
