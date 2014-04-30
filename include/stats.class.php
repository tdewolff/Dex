<?php

class Stats
{
	public static function registerPageVisit()
	{
		if (!Db::isValid())
			return false;

		if (preg_match('/robot|spider|crawler|curl|bot|^$/i', $_SERVER['HTTP_USER_AGENT']))
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
			while (isset($oldTime))
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

	public static function referralStats($limit = null)
	{
		$urls = array();
		$keywords = array();
		$table = Db::query("SELECT *, COUNT(referral) AS n FROM stats GROUP BY referral ORDER BY n DESC;");
		while ($row = $table->fetch())
		{
			$urls[] = array(
				'url' => $row['referral'],
				'n' => $row['n']
			);

			if (preg_match('/^(https?):\/\/(.*)\//', $row['referral'], $matches))
			{
				$domain = $matches[2];
				$levels = explode('.', $domain);
				if (count($levels) > 1)
					$domain = $levels[count($levels) - 2];

				$query_position = strrpos($row['referral'], '?');
				if ($query_position === false)
					continue;
				$query = substr($row['referral'], $query_position + 1);
				parse_str($query, $parameters);

				switch ($domain)
				{
				case 'google':
				case 'ask':
				case 'bing':
				case 'aol':
				case 'alltheweb':
					$parameter_key = 'q';
					break;
				case 'yahoo':
					$parameter_key = 'p';
					break;
				case 'baidu':
					$parameter_key = 'wd';
					break;
				case 'yandex':
					$parameter_key = 'text';
					break;
				default:
					continue;
				}

				if (!isset($parameters[$parameter_key]))
					continue;

				$query_keywords = explode('+', $parameters[$parameter_key]);
				foreach ($query_keywords as $keyword)
				{
					$keyword = urldecode($keyword);
					if (isset($keywords[$keyword]))
						$keywords[$keyword] += $row['n'];
					else
						$keywords[$keyword] = $row['n'];
				}
			}
		}

		arsort($keywords);

		$oldKeywords = $keywords;
		$keywords = array();
		foreach ($oldKeywords as $n => $keyword)
			$keywords[] = array(
				'keyword' => $keyword,
				'n' => $n
			);

		return array(
			'urls' => array_slice($urls, 0, $limit),
			'keywords' => array_slice($keywords, 0, $limit)
		);
	}
}
