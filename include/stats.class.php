<?php

class Stats
{
	public static function registerPageVisit()
	{
		if (!Db::isValid())
			return false;

		if (preg_match('/robot|spider|crawler|curl|^$/i', $_SERVER['HTTP_USER_AGENT']))
			return false;

		$stat = Db::singleQuery("SELECT * FROM stats WHERE end_time >= '" . Db::escape(time() - 60 * 30) . "' AND ip_address = '" . Db::escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1;");
		if (!$stat)
			Db::exec("INSERT INTO stats (n, time, end_time, ip_address, request_url, referral) VALUES (
				'1',
				'" . Db::escape(time()) . "',
				'" . Db::escape(time()) . "',
				'" . Db::escape($_SERVER['REMOTE_ADDR']) . "',
				'" . Db::escape(Common::$request_url) . "',
				'" . Db::escape(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') . "'
			);");
		else
			Db::exec("UPDATE stats SET n = n + 1, end_time = '" . Db::escape(time()) . "' WHERE stat_id = '" . Db::escape($stat['stat_id']) . "';");
	}

	public static function pageVisitChart()
	{
		$visits = array();
		$table = Db::query("SELECT * FROM stats ORDER BY time ASC;");
		while ($row = $table->fetch())
		{
			$time = floor($row['time'] / 86400) * 86400;
			while ($oldTime)
			{
				$oldTime += 86400;
				if ($oldTime >= $time)
					break;

				$visits[] = array(
					'date' => $oldTime,
					'visits' => 0,
					'unique_visits' => 0
				);
			}
			$oldTime = $time;

			if (!count($visits) || $visits[count($visits) - 1]['date'] !== $time)
				$visits[] = array(
					'date' => $time,
					'visits' => $row['n'],
					'unique_visits' => 1
				);
			else
			{
				$visits[count($visits) - 1]['visits'] += $row['n'];
				$visits[count($visits) - 1]['unique_visits']++;
			}
		}

		$now = floor(time() / 86400) * 86400;
		if (count($visits))
			while ($visits[count($visits) - 1]['date'] < $now)
				$visits[] = array(
					'date' => $visits[count($visits) - 1]['date'] + 86400,
					'visits' => 0,
					'unique_visits' => 0
				);

		return $visits;
	}
}

?>