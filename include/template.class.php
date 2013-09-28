<?php

require_once('include/smarty/Smarty.class.php');

class Template
{
	static $smarty;
	
	private function __constructor() {}
	
	public static function init()
	{		
		$smarty = new Smarty();
		$smarty->setTemplateDir('templates/');
		$smarty->setCompileDir('include/smarty/templates_c/');
		$smarty->setCacheDir('include/smarty/cache/');
		$smarty->setConfigDir('include/smarty/configs/');
		$smarty->addPluginsDir('include/smarty_plugins/');
	}
}

?>