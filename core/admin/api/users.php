<?php

if (!Session::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

if (API::action('delete_user'))
{
	if (!API::has('user_id') || API::get('user_id') == Session::getUserId())
		user_error('No user ID set or user ID equals current user ID', ERROR);

	$db->exec("DELETE FROM user WHERE user_id = '" . $db->escape(API::get('user_id')) . "';");
	API::finish();
}

$users = array();
$table = $db->query("SELECT * FROM user;");
while ($row = $table->fetch())
{
	$row['current'] = $row['user_id'] == Session::getAccountId();
	$row['permission'] = ucfirst($row['permission']);
	$users[] = $row;
}

API::set('users', $users);
API::finish();

?>
