<?php

if (!User::isAdmin())
	user_error('Forbidden access', ERROR);

if (API::action('delete_user'))
{
	if (!API::has('user_id') || API::get('user_id') == User::getUserId())
		user_error('No user ID set or user ID equals current user ID', ERROR);

	$db->exec("DELETE FROM user WHERE user_id = '" . $db->escape(API::get('user_id')) . "';");
	API::finish();
}
else if (API::action('get_users'))
{
    $users = array();
    $table = $db->query("SELECT * FROM user;");
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
