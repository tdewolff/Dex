<?php

if (isset($uri[2]) && $uri[2] == 'logs' && isset($uri[3]))
{
	if ($uri[3] == 'view')
	{
		$log = file_get_contents('logs/' . Log::getCurrentFilename());

		Hooks::emit('header');

		Dexterous::assign('log', $log);
		Dexterous::render('admin/log.tpl');

		Hooks::emit('footer');
		exit;
	}
	else if ($uri[3] == 'clear' && Session::isAdmin())
	{
		$handle = opendir('logs/');
		while (($log_name = readdir($handle)) !== false)
			if (is_file('logs/' . $log_name))
				unlink('logs/' . $log_name);
	}
}
elseif (isset($uri[2]) && $uri[2] == 'cache' && isset($uri[3]) && $uri[3] == 'clear' && Session::isAdmin())
{
	$handle = opendir('resources/cache/');
	while (($cache_name = readdir($handle)) !== false)
		if (is_file('resources/cache/' . $cache_name))
			unlink('resources/cache/' . $cache_name);
}

$logs_size = 0;
$handle = opendir('logs/');
while (($log_name = readdir($handle)) !== false)
	if (is_file('logs/' . $log_name))
		$logs_size += filesize('logs/' . $log_name);

$cache_size = 0;
$handle = opendir('resources/cache/');
while (($cache_name = readdir($handle)) !== false)
	if (is_file('resources/cache/' . $cache_name))
		$cache_size += filesize('resources/cache/' . $cache_name);

Dexterous::addStyle('resources/styles/popbox.css');
Dexterous::addDeferredScript('resources/scripts/popbox.js');

Hooks::emit('header');

Dexterous::assign('log_name_current', Log::getCurrentFilename());
Dexterous::assign('logs_size', Common::formatBytes($logs_size));
Dexterous::assign('logs_size_percentage', number_format(100 * $logs_size / 50 / 1000 / 1000, 1));
Dexterous::assign('cache_size', Common::formatBytes($cache_size));
Dexterous::assign('cache_size_percentage', number_format(100 * $cache_size / 250 / 1000 / 1000, 1));
Dexterous::render('admin/index.tpl');

Hooks::emit('footer');
exit;

?>
