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
		$details = Log::getLoglineDetails($logline);
		if (!$details)
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

		$details['message'] = htmlentities($details['message']);
		$details['html'] = htmlentities(Error::formatError($details['message'], $details['location'], $details['backtrace']));
		$logs[] = $details;
	}
	API::set('logs', $logs);
	API::finish();
}

?>