<?php

class Session
{
	public static function logIn($id, $userlevel)
	{
		$_SESSION['logged_id'] = $id;
		$_SESSION['logged_userlevel'] = $userlevel;
		$_SESSION['logged_time'] = time();
	}

	public static function logOut()
	{
		unset($_SESSION['logged_id']);
		unset($_SESSION['logged_userlevel']);
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
		return (isset($_SESSION['logged_userlevel']) && $_SESSION['logged_userlevel'] == 0);
	}
}

?>