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
	if (!$total)
		user_error('Dex uses no diskspace but I don\'t believe it', WARNING);

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
