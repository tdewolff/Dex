<?php

require_once('include/stats.class.php');

if (!User::loggedIn())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}

if (API::action('get_visits'))
{
	API::set('visits', Stats::pageVisitChart());
	API::finish();
}
