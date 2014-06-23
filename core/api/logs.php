<?php

if (!User::loggedIn())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('get'))
{
	$errors_only = API::has('errors_only') ? API::get('errors_only') : false;
	$loglines = Log::getLastLines(API::get('lines'), $errors_only || !API::has('lines'));

	$logs = array();
	foreach (array_reverse($loglines) as $logline)
	{
		try
		{
			$datetime = new DateTime($logline['datetime']);
		}
		catch (Exception $e)
		{
			continue;
		}

		if ($errors_only)
		{
			if (count($logs) >= API::get('lines') || $datetime->diff(new DateTime())->m > 0)
				break;

			if ($logline['type'] != 'ERROR' && $logline['type'] != 'WARNING')
				continue;
		}

		$logline['message'] = htmlentities($logline['message']);
		$logline['html'] = htmlentities(Error::formatError($logline['message'], $logline['location'], $logline['backtrace']));
		$logs[] = $logline;
	}
	API::set('logs', $logs);
	API::finish();
}
