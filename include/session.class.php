<?php

class Session
{
	public static function logIn($user_id, $permission)
	{
		$_SESSION['login_id'] = $user_id;
		$_SESSION['login_permission'] = $permission;
		$_SESSION['login_time'] = time();
	}

	public static function logOut()
	{
		unset($_SESSION['login_id']);
		unset($_SESSION['login_permission']);
		unset($_SESSION['login_time']);
	}

	public static function isUser()
	{
		if (isset($_SESSION['login_id']) &&
			isset($_SESSION['login_time']) &&
			$_SESSION['login_id'] > 0 &&
			$_SESSION['login_time'] + 1800 > time()) // 30 minutes
		{
			$_SESSION['login_time'] = time();
			return true;
		}

		Session::logOut();
		return false;
	}

	public static function isAdmin()
	{
		return (self::isUser() && isset($_SESSION['login_permission']) && $_SESSION['login_permission'] == 'admin');
	}

	public static function getUserId()
	{
		return isset($_SESSION['login_id']) ? $_SESSION['login_id'] : false;
	}

	public static function getUsername()
	{
		global $db;

		if (isset($_SESSION['login_id']))
		{
			$user = $db->querySingle("SELECT * FROM user WHERE user_id = '" . $_SESSION['login_id'] . "' LIMIT 1;");
			return $user['username'];
		}
		return false;
	}

	public static function getPermission()
	{
		return isset($_SESSION['login_permission']) ? $_SESSION['login_permission'] : false;
	}
}

?>