<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

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
    require_once('include/file.php');

    $lines = API::has('lines') ? API::get('lines') : 100;

    $logs = array();
    $logfile = array_reverse(tail(Log::getFilename(), $lines));
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

?>