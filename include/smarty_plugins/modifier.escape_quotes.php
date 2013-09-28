<?php

function smarty_modifier_escape_quotes($string) {
   return strtr($string, array('"' => '&quot;', '\'' => '\\\''));
}

?>
