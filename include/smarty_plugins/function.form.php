<?php

function smarty_function_form($params, $smarty) {
	$smarty->assign('form', $params['data']);
	return $smarty->fetch('include/smarty_plugins/templates/form.tpl');
}

?>
