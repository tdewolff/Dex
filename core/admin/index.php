<?php

if (isset($url[1]) && $url[1] == 'logs')
{
	Hooks::emit('admin-header');
	Core::render('admin/logs.tpl');
	Hooks::emit('admin-footer');
	exit;
}

$log_name = Log::getFilename();
$slash_position = strrpos($log_name, '/');
$log_name = $slash_position ? substr($log_name, $slash_position + 1) : $log_name;

Hooks::emit('admin-header');

Core::set('log_name', $log_name);
Core::render('admin/index.tpl');

Hooks::emit('admin-footer');
exit;

?>
