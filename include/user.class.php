<?php

define('SESSION_TIME', 1800); // 30 minutes

class User
{
	public static function logIn($user_id)
	{
		$user = Db::querySingle("SELECT * FROM user WHERE user_id = '" . $user_id . "' LIMIT 1;");
		if (!$user)
			user_error('Could not login', ERROR);

		$_SESSION['user'] = array(
			'user_id' => $user_id,
			'username' => $user['username'],
			'email' => $user['email'],
			'role' => $user['role'],
			'time' => time()
		);
	}

	public static function logOut()
	{
		unset($_SESSION['user']);
	}

	public static function loggedIn()
	{
		if (!isset($_SESSION['user']))
			return false;

		if ($_SESSION['user']['time'] + SESSION_TIME > time())
			if (Db::isValid() && Db::querySingle("SELECT * FROM user WHERE user_id = '" . $_SESSION['user']['user_id'] . "' LIMIT 1;"))
				return true;

		return false;
	}

	public static function refreshLogin()
	{
		if (self::loggedIn())
			$_SESSION['user']['time'] = time();
	}

	public static function isEditor()
	{
		return (self::loggedIn() ? $_SESSION['user']['role'] == 'editor' : false);
	}

	public static function isAdmin()
	{
		return (self::loggedIn() ? $_SESSION['user']['role'] == 'admin' : false);
	}

	public static function getUserId()
	{
		return (self::loggedIn() ? $_SESSION['user']['user_id'] : false);
	}

	public static function getUsername()
	{
		return (self::loggedIn() ? $_SESSION['user']['username'] : false);
	}

	public static function getEmail()
	{
		return (self::loggedIn() ? $_SESSION['user']['email'] : false);
	}

	public static function getRole()
	{
		return (self::loggedIn() ? $_SESSION['user']['role'] : false);
	}

	public static function getTimeleft()
	{
		return (isset($_SESSION['user']) ? ($_SESSION['user']['time'] + SESSION_TIME - time()) : false);
	}
}

?>