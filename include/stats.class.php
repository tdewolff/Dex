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

	private static function organicSourceParameter($host)
	{
		static $organic_sources = array('www.google' => 'q',
										'daum.net' => 'q',
										'eniro.se' => 'search_word',
										'naver.com' => 'query',
										'yahoo.com' => 'p',
										'msn.com' => 'q',
										'bing.com' => 'q',
										'aol.com' => 'query',
										'lycos.com' => 'query',
										'ask.com' => 'q',
										'altavista.com' => 'q',
										'search.netscape.com' => 'query',
										'cnn.com' => 'query',
										'about.com' => 'terms',
										'mamma.com' => 'query',
										'alltheweb.com' => 'q',
										'voila.fr' => 'rdata',
										'search.virgilio.it' => 'qs',
										'baidu.com' => 'wd',
										'alice.com' => 'qs',
										'yandex.com' => 'text',
										'najdi.org.mk' => 'q',
										'aol.com' => 'q',
										'mamma.com' => 'query',
										'seznam.cz' => 'q',
										'search.com' => 'q',
										'wp.pl' => 'szukai',
										'online.onetcenter.org' => 'qt',
										'szukacz.pl' => 'q',
										'yam.com' => 'k',
										'pchome.com' => 'q',
										'kvasir.no' => 'q',
										'sesam.no' => 'q',
										'ozu.es' => 'q',
										'terra.com' => 'query',
										'mynet.com' => 'q',
										'ekolay.net' => 'q',
										'rambler.ru' => 'words'
		);

		foreach ($organic_sources as $source => $parameter)
			if (strpos($host, $source) !== false)
				return $parameter;
		return false;
	}

	public static function rsort($a, $b)
	{
		if ($a['n'] == $b['n'])
			return 0;
		return ($a['n'] > $b['n']) ? -1 : 1;
	}

	public static function referralStats($limit = null)
	{
		$urls = array();
		$keywords = array();
		$table = Db::query("SELECT referral FROM stats;");
		while ($row = $table->fetch())
		{
			$url = parse_url($row['referral']);
			$name = $url['host'] . $url['path'];
			if (isset($urls[$name]))
				$urls[$name]['n']++;
			else
				$urls[$name] = array(
					'url' => (empty($name) ? '' : $url['scheme'] . '://' . $url['host'] . $url['path']),
					'name' => (empty($name) ? '(' . __('direct') . ')' : $name),
					'n' => 1
				);

			parse_str($url['query'], $query);
			$parameter = self::organicSourceParameter($url['host']);
			if (!isset($query[$parameter]))
				continue;

			$query_keywords = explode(' ', urldecode($query[$parameter]));
			foreach ($query_keywords as $keyword)
				if (isset($keywords[$keyword]))
					$keywords[$keyword]['n']++;
				else
					$keywords[$keyword] = array(
					'keyword' => $keyword,
					'n' => 1
				);
		}

		$urls = array_values(array_slice($urls, 0, $limit));
		$keywords = array_values(array_slice($keywords, 0, $limit));

		usort($urls, array('self', 'rsort'));
		usort($keywords, array('self', 'rsort'));

		return array(
			'urls' => $urls,
			'keywords' => $keywords
		);
	}
}
