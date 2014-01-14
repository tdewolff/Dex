<?php

define('SESSION_TIME', 1800); // 30 minutes

class User
{
	public static function logIn($user_id)
	{
		global $db;

		$user = $db->querySingle("SELECT * FROM user WHERE user_id = '" . $user_id . "' LIMIT 1;");
		if (!$user)
			user_error('Could not login', ERROR);

		$_SESSION['login'] = array(
			'user_id' => $user_id,
			'username' => $user['username'],
			'email' => $user['email'],
			'role' => $user['role'],
			'time' => time()
		);
	}

	public static function logOut()
	{
		unset($_SESSION['login']);
	}

	public static function loggedIn()
	{
		global $db;

		if (!isset($_SESSION['login']))
			return false;

		if ($_SESSION['login']['time'] + SESSION_TIME > time())
			if (filesize($db->filename) != 0 && $db->querySingle("SELECT * FROM user WHERE user_id = '" . $_SESSION['login']['user_id'] . "' LIMIT 1;"))
				return true;

		self::logOut();
		return false;
	}

	public static function refreshLogin()
	{
		if (self::loggedIn())
			$_SESSION['login']['time'] = time();
	}

	public static function isEditor()
	{
		return (self::loggedIn() ? $_SESSION['login']['role'] == 'editor' : false);
	}

	public static function isAdmin()
	{
		return (self::loggedIn() ? $_SESSION['login']['role'] == 'admin' : false);
	}

	public static function getUserId()
	{
		return (self::loggedIn() ? $_SESSION['login']['user_id'] : false);
	}

	public static function getUsername()
	{
		return (self::loggedIn() ? $_SESSION['login']['username'] : false);
	}

	public static function getEmail()
	{
		return (self::loggedIn() ? $_SESSION['login']['email'] : false);
	}

	public static function getRole()
	{
		return (self::loggedIn() ? $_SESSION['login']['role'] : false);
	}

	public static function getTimeleft()
	{
		return (self::loggedIn() ? ($_SESSION['login']['time'] + SESSION_TIME - time()) : false);
	}
}

?>