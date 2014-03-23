<?php

class Stats
{
	public static function registerPageVisit()
	{
		if (!Db::isValid())
			return false;

		$stat = Db::singleQuery("SELECT * FROM stats WHERE time >= '" . Db::escape(time() - 60 * 30) . "' AND ip_address = '" . Db::escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1;");
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
		$visits = array();
		$table = Db::query("SELECT * FROM stats ORDER BY time ASC;");
		while ($row = $table->fetch())
			if (!count($visits) || date('M j', $visits[count($visits) - 1]['date']) !== date('M j', $row['time']))
				$visits[] = array(
					'date' => floor($row['time'] / 86400) * 86400,
					'visits' => $row['n'],
					'unique_visits' => 1
				);
			else
			{
				$visits[count($visits) - 1]['visits'] += $row['n'];
				$visits[count($visits) - 1]['unique_visits']++;
			}
		return $visits;
	}
}

?>