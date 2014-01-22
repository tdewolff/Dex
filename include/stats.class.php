<?php

class Stats
{
    public static function registerPageVisit()
    {
        if (Db::isValid())
            return false;

        if (!Db::querySingle("SELECT * FROM stats WHERE time >= '" . Db::escape(time() - 60 * 60) . "' AND ip_address = '" . Db::escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1;"))
            Db::exec("INSERT INTO stats (time, ip_address) VALUES (
                '" . Db::escape(time()) . "',
                '" . Db::escape($_SERVER['REMOTE_ADDR']) . "'
            );");
    }

    public static function pageVisitChart()
    {
        $page_visits = array();
        $table = Db::query("SELECT * FROM stats;");
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