<?php

class Dexterous
{
	private static $titles = array();
	private static $styles = array();
	private static $scripts = array();
	private static $contents = array();

	public function __construct()
	{
		global $smarty;
		$smarty->setTemplateDir('templates/');
	}

	////////////////

	public static function assign($key, $value)
	{
		global $smarty;
		$smarty->assign($key, $value);
	}

	public static function render($template)
	{
		global $smarty;
		$smarty->display($template);
	}

	public static function renderModule($module, $role, $template)
	{
		global $smarty;
		self::$contents[$role][] = $smarty->fetch('modules/' . $module . '/templates/' . $template);
	}

	////////////////

	public static function setModule($module)
	{
		global $smarty;
		$smarty->setTemplateDir(array('templates/', 'modules/' . $module . '/templates/'));
	}

	public static function addTitle($title) {
		self::$titles[] = $title;
	}

	public static function addStyle($style) {
		self::$styles[] = $style;
	}

	public static function addScript($script) {
		self::$scripts['header'][] = $script;
	}

	public static function addDeferredScript($script) {
		self::$scripts['footer'][] = $script;
	}

	////////////////

	public static function getTitles() {
		return self::$titles;
	}

	public static function getScripts($place) {
		return isset(self::$scripts[$place]) ? self::$scripts[$place] : array();
	}

	public static function getStyles() {
		return self::$styles;
	}

	public static function getContents() {
		return self::$contents;
	}
}

?>
