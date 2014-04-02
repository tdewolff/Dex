<?php

// doesn't have to be logged in for these functions
if (API::action('timeleft'))
{
	API::set('timeleft', User::getTimeleft());
	API::finish();
}
else if (API::action('forget'))
{
	User::forget();
	API::finish();
}
else if (API::action('logout'))
{
	if (API::has('admin') && API::get('admin') == 1 && isset($_SESSION['last_site_request']))
		unset($_SESSION['last_site_request']);

	User::logOut();
	API::finish();
}

if (!User::isAdmin())
	user_error('Forbidden access', ERROR);

if (API::action('delete_user'))
{
	if (!API::has('user_id') || API::get('user_id') == User::getUserId())
		user_error('No user ID set or user ID equals current user ID', ERROR);

	Db::exec("DELETE FROM user WHERE user_id = '" . Db::escape(API::get('user_id')) . "';");
	API::finish();
}
else if (API::action('get_users'))
{
	$users = array();
	$table = Db::query("SELECT * FROM user;");
	while ($row = $table->fetch())
	{
		$row['current'] = $row['user_id'] == User::getUserId();
		$row['role'] = ucfirst($row['role']);
		$users[] = $row;
	}
	API::set('users', $users);
	API::finish();
}

?>
