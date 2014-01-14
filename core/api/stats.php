<?php

if (!User::isAdmin())
    user_error('Forbidden access', ERROR);

if (API::action('page-visits'))
{
    $page_visits = array();
    /*$table = $db->query("SELECT * FROM stats;");
    while ($row = $table->fetch())
    {
        if (!isset($page_visits[date('M j', time())]))
            $page_visits[date('M j', time())] = 1;
        else
            $page_visits[date('M j', time())]++;
    }*/

    $prev = 20;
    for ($i = 0; $i < 31; $i++) {
        $page_visits[] = array(
            'date' => time() - ((31 - $i) * 24 * 60 * 60),
            'visits' => rand($prev - 5, $prev + 10)
        );
        $prev = $page_visits[count($page_visits) - 1]['visits'];
    }

    API::set('page-visits', $page_visits);
    API::finish();
}

?>