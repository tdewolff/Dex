<?php

if (!User::isAdmin())
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

Hooks::emit('admin-header');
Core::render('admin/admin.tpl');
Hooks::emit('admin-footer');
exit;

?>
