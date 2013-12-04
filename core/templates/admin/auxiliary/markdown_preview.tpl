<?php

use \Michelf\Markdown;
require_once('vendor/markdown.php');
require_once('vendor/smartypants.php');

echo SmartyPants(Markdown::defaultTransform($_POST['data']));

?>