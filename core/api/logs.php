<?php

if (!User::loggedIn())
    user_error('Forbidden access', ERROR);

if (API::action('get'))
{
    $errors = API::has('errors') ? API::get('errors') : false;
    if ($errors || !API::has('lines'))
        $logfile = array_reverse(Log::getAllLines());
    else
        $logfile = array_reverse(Log::getLastLines(API::get('lines')));

    $logs = array();
    $oldDatetime = false;
    foreach ($logfile as $logline)
    {
        $logline = explode(' ', $logline);
        $datetime = new DateTime(substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1));

        if ($errors)
        {
            if (count($logs) >= API::get('lines') || $datetime->diff(new DateTime())->m > 0)
                break;

            if ($logline[3] != 'ERROR' && $logline[3] != 'WARNING')
                continue;
        }
        else
        {
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

?>