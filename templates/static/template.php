<?php

use \Michelf\Markdown;
require_once('include/libs/markdown.php');
require_once('include/libs/smartypants.php');

echo SmartyPants(Markdown::defaultTransform($_['content']));

?>