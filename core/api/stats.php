<?php

require_once('include/stats.class.php');

if (!User::loggedIn())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('get_visits'))
{
	$days = null;
	if (API::has('days'))
		$days = API::get('days');

	API::set('visits', Stats::pageVisitChart($days));
	API::finish();
}
else if (API::action('get_referrals'))
{
	$limit = null;
	if (API::has('limit'))
		$limit = API::get('limit');

	API::set('referrals', Stats::referralStats($limit));
	API::finish();
}
