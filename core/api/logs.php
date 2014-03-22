<?php

if (!User::loggedIn())
	user_error('Forbidden access', ERROR);

if (API::action('get'))
{
	$errors = API::has('errors') ? API::get('errors') : false;
	if ($errors || !API::has('lines'))
		$loglines = array_reverse(Log::getAllLines());
	else
		$loglines = array_reverse(Log::getLastLines(API::get('lines')));

	$logs = array();
	foreach ($loglines as $logline)
	{
		$backtrace = '';
		$backtrace_pos = strpos($logline, '[', 1);
		if ($backtrace_pos !== false)
		{
			$backtrace = json_decode(substr($logline, $backtrace_pos), true);
			$logline = substr($logline, 0, $backtrace_pos);
		}

		$logline = explode(' ', $logline);
		if (count($logline) < 4)
			continue;

		try
		{
			$datetime = new DateTime(substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1));
		}
		catch (Exception $e)
		{
			continue;
		}

		if ($errors)
		{
			if (count($logs) >= API::get('lines') || $datetime->diff(new DateTime())->m > 0)
				break;

			if ($logline[3] != 'ERROR' && $logline[3] != 'WARNING')
				continue;
		}

		$message = substr(implode(' ', array_slice($logline, 3)), 8);
		$logs[] = array(
			'datetime' => substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1),
			'ipaddress' => $logline[2],
			'type' => $logline[3],
			'message' => htmlentities($message),
			'html' => htmlentities(Error::formatError($message, $backtrace))
		);
	}
	API::set('logs', $logs);
	API::finish();
}

?>