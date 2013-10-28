<?php

class Session
{
	public static function logIn($account_id, $permission)
	{
		$_SESSION['logged_id'] = $account_id;
		$_SESSION['logged_permission'] = $permission;
		$_SESSION['logged_time'] = time();
	}

	public static function logOut()
	{
		unset($_SESSION['logged_id']);
		unset($_SESSION['logged_permission']);
		unset($_SESSION['logged_time']);
	}

	public static function isUser()
	{
		if (isset($_SESSION['logged_id']) &&
			isset($_SESSION['logged_time']) &&
			$_SESSION['logged_id'] > 0 &&
			$_SESSION['logged_time'] + 1800 > time()) // 30 minutes
		{
			$_SESSION['logged_time'] = time();
			return true;
		}

		Session::logOut();
		return false;
	}

	public static function isAdmin()
	{
		return (self::isUser() && isset($_SESSION['logged_permission']) && $_SESSION['logged_permission'] == 0);
	}

	public static function getAccountId()
	{
		return isset($_SESSION['logged_id']) ? $_SESSION['logged_id'] : 0;
	}
}

?>