<?php

class Stats
{
    public static function registerPageVisit()
    {
        global $db;

        if (!$db->querySingle("SELECT * FROM stats WHERE time >= '" . $db->escape(time() - 60 * 60) . "' AND ip_address = '" . $db->escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1;"))
            $db->exec("INSERT INTO stats (time, ip_address) VALUES (
                '" . $db->escape(time()) . "',
                '" . $db->escape($_SERVER['REMOTE_ADDR']) . "'
            );");
    }

    public static function pageVisitChart()
    {
        global $db;

        $page_visits = array();
        $table = $db->query("SELECT * FROM stats;");
        while ($row = $table->fetch())
        {
            if (!isset($page_visits[date('M j', time())]))
                $page_visits[date('M j', time())] = 1;
            else
                $page_visits[date('M j', time())]++;
        }

        // TODO: implement
    }
}

?>