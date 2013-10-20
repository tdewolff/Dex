<?php

class Dexterous
{
	public static $vars = array();
	public static $titles = array();
	public static $styles = array();
	public static $scripts = array();

	////////////////

	public static function assign($key, $value) {
		self::$vars[$key] = $value;
	}

	public static function addTitle($title) {
		self::$titles[] = $title;
	}
}

class Core extends Dexterous
{
	public static function render($_template) {
		$_ = self::$vars;
		include('core/templates/' . $_template);
	}

	public static function addStyle($style) {
		self::$styles[] = 'core/resources/styles/' . $style;
	}

	public static function addScript($script) {
		self::$scripts['header'][] = 'core/resources/scripts/' . $script;
	}

	public static function addDeferredScript($script) {
		self::$scripts['footer'][] = 'core/resources/scripts/' . $script;
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
}

class Module extends Dexterous
{
	public static $module = '';

	public static function set($module) {
		self::$module = $module;
	}

	public static function render($_template) {
		$_ = self::$vars;
		include('modules/' . self::$module . '/templates/' . $_template);
	}

	public static function addStyle($style) {
		self::$styles[] = 'modules/' . self::$module . '/resources/styles/' . $style;
	}

	public static function addScript($script) {
		self::$scripts['header'][] = 'modules/' . self::$module . '/resources/scripts/' . $script;
	}

	public static function addDeferredScript($script) {
		self::$scripts['footer'][] = 'modules/' . self::$module . '/resources/scripts/' . $script;
	}
}

class Theme extends Dexterous
{
	public static $theme = '';

	public static function set($theme) {
		self::$theme = $theme;
	}

	public static function render($_template) {
		$_ = self::$vars;
		include('themes/' . self::$theme . '/templates/' . $_template);
	}

	public static function addStyle($style) {
		self::$styles[] = 'themes/' . self::$theme . '/resources/styles/' . $style;
	}

	public static function addScript($script) {
		self::$scripts['header'][] = 'themes/' . self::$theme . '/resources/scripts/' . $script;
	}

	public static function addDeferredScript($script) {
		self::$scripts['footer'][] = 'themes/' . self::$theme . '/resources/scripts/' . $script;
	}
}

?>
