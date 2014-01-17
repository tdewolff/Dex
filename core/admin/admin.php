<?php

if (isset($url[1]) && $url[1] == 'logs')
{
	Hooks::emit('admin-header');
	Core::render('admin/logs.tpl');
	Hooks::emit('admin-footer');
	exit;
}

$logs_size = 0;
$handle = opendir('logs/');
while (($log_name = readdir($handle)) !== false)
	if (is_file('logs/' . $log_name))
		$logs_size += filesize('logs/' . $log_name);

$cache_size = 0;
$handle = opendir('cache/');
while (($cache_name = readdir($handle)) !== false)
	if (is_file('cache/' . $cache_name))
		$cache_size += filesize('cache/' . $cache_name);

$log_name = Log::getFilename();
$slash_position = strrpos($log_name, '/');
$log_name = $slash_position ? substr($log_name, $slash_position + 1) : $log_name;

Hooks::emit('admin-header');

Core::assign('log_name', $log_name);
Core::assign('logs_size', Common::formatBytes($logs_size));
Core::assign('logs_size_percentage', number_format(100 * $logs_size / 50 / 1000 / 1000, 1));
Core::assign('cache_size', Common::formatBytes($cache_size));
Core::assign('cache_size_percentage', number_format(100 * $cache_size / 250 / 1000 / 1000, 1));
Core::render('admin/admin.tpl');

Hooks::emit('admin-footer');
exit;

?>
