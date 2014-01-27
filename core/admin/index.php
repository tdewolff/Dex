<?php

if (isset($url[1]) && $url[1] == 'logs')
{
	Hooks::emit('admin-header');
	Core::render('admin/logs.tpl');
	Hooks::emit('admin-footer');
	exit;
}

Hooks::emit('admin-header');
Core::render('admin/index.tpl');
Hooks::emit('admin-footer');
exit;

?>
