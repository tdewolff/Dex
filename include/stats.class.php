<?php

class Stats
{
    public static function registerPageVisit()
    {
        if (!Db::isValid())
            return false;

        $stat = Db::querySingle("SELECT * FROM stats WHERE time >= '" . Db::escape(time() - 60 * 30) . "' AND ip_address = '" . Db::escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1;");
        if (!$stat)
            Db::exec("INSERT INTO stats (n, time, ip_address, request_url, referral) VALUES (
                '1',
                '" . Db::escape(time()) . "',
                '" . Db::escape($_SERVER['REMOTE_ADDR']) . "',
                '" . Db::escape(Common::$request_url) . "',
                '" . Db::escape(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') . "'
            );");
        else
            Db::exec("UPDATE stats SET n = n + 1 WHERE stat_id = '" . Db::escape($stat['stat_id']) . "';");
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