<?php

if (!User::isAdmin())
	user_error('Forbidden access', ERROR);

if (API::action('clear_logs'))
{
	Log::close();

	$handle = opendir('logs/');
	while (($log_name = readdir($handle)) !== false)
		if (is_file('logs/' . $log_name))
			unlink('logs/' . $log_name);

	Log::open();
	API::finish();
}
else if (API::action('clear_cache'))
{
	$handle = opendir('cache/');
	while (($cache_name = readdir($handle)) !== false)
		if (is_file('cache/' . $cache_name))
			unlink('cache/' . $cache_name);
	API::finish();
}
else if (API::action('get_logs'))
{
	$lines = API::has('lines') ? API::get('lines') : 100;

	$logs = array();
	$logfile = array_reverse(Log::getLastLines($lines));
	$oldDatetime = false;
	foreach ($logfile as $logline)
	{
		$logline = explode(' ', $logline);

		$datetime = new DateTime(substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1));
		if (!$oldDatetime)
			$oldDatetime = $datetime;
		else if ($oldDatetime->diff($datetime)->s > 1)
		{
			$logs[] = array(
				'datetime' => '',
				'ipaddress' => '',
				'type' => '',
				'message' => ''
			);
			$oldDatetime = $datetime;
		}

		$logs[] = array(
			'datetime' => substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1),
			'ipaddress' => $logline[2],
			'type' => $logline[3],
			'message' => htmlentities(implode(' ', array_slice($logline, 4)))
		);
	}
	API::set('logs', $logs);
	API::finish();
}
else if (API::action('diskspace_usage'))
{
	$total = Common::getDirectorySize('./');
	$rest = $total;

	$diskspace = array(null);
	$directories = array('modules/', 'templates/', 'themes/', 'assets/', 'cache/');
	foreach ($directories as $directory)
	{
		$size = Common::getDirectorySize($directory);
		$diskspace[] = array(
			'name' => ucfirst(substr($directory, 0, -1)),
			'size' => Common::formatBytes($size),
			'percentage' => $size / $total * 100.0);
		$rest -= $size;
	}

	$diskspace[0] = array(
		'name' => 'Dex',
		'size' => Common::formatBytes($rest),
		'percentage' => $rest / $total * 100.0);

	API::set('diskspace', $diskspace);
	API::set('diskspace_total', $total);
	API::finish();
}
else if (API::action('get_warnings'))
{
	$warnings = array();

	$apache_modules = apache_get_modules();
	if (!in_array('mod_deflate', $apache_modules))
		$warnings[] = 'Apache module mod_deflate is not enabled';
	if (!in_array('mod_expires', $apache_modules))
		$warnings[] = 'Apache module mod_expires is not enabled';
	if (!in_array('mod_filter', $apache_modules))
		$warnings[] = 'Apache module mod_filter is not enabled';
	if (!in_array('mod_headers', $apache_modules))
		$warnings[] = 'Apache module mod_headers is not enabled';

	if (!$config['minifying'])
		$warnings[] = 'Minifying is disabled in config.ini';
	if (!$config['caching'])
		$warnings[] = 'Caching is disabled in config.ini';
	if ($config['verbose_logging'])
		$warnings[] = 'Verbose logging is enabled in config.ini';
	if ($config['display_errors'])
		$warnings[] = 'Displaying errors is enabled in config.ini';
	if ($config['display_notices'])
		$warnings[] = 'Displaying notices is enabled in config.ini';

	API::set('warnings', $warnings);
	API::finish();
}

?>