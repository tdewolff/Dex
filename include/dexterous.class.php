<?php

class Dexterous
{
	private static $vars = array();
	private static $titles = array();
	private static $styles = array();
	private static $scripts = array();

	public function __construct() {
	}

	////////////////

	public static function assign($key, $value) {
		self::$vars[$key] = $value;
	}

	public static function render($_template) {
		$_ = self::$vars;
		include('templates/' . $_template);
	}

	public static function renderModule($_module, $_template) {
		$_ = self::$vars;
		include('modules/' . $_module . '/templates/' . $_template);
	}

	////////////////

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
