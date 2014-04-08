<?php

if (!User::loggedIn())
{
	http_response_code(403);
	user_error('Forbidden access', ERROR);
}

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
			$datetime = new DateTime($details['datetime']);
		}
		catch (Exception $e)
		{
			continue;
		}

		if ($errors)
		{
			if (count($logs) >= API::get('lines') || $datetime->diff(new DateTime())->m > 0)
				break;

			if ($details['type'] != 'ERROR' && $details['type'] != 'WARNING')
				continue;
		}

		$details['message'] = htmlentities($details['message']);
		$details['html'] = htmlentities(Error::formatError($details['message'], $details['location'], $details['backtrace']));
		$logs[] = $details;
	}
	API::set('logs', $logs);
	API::finish();
}
