<?php

if (!User::isAdmin())
{
	Common::responseCode(403);
	user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);
}

Hooks::emit('admin-header');
Core::render('admin/templates.tpl');
Hooks::emit('admin-footer');
exit;
