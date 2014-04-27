<?php

class Dex
{
	protected static $vars = array();
	protected static $titles = array();
	protected static $externalStyles = array();
	protected static $styles = array();
	protected static $externalScripts = array('header' => array(), 'footer' => array());
	protected static $scripts = array('header' => array(), 'footer' => array());

	protected static $link_id = 0;
	protected static $theme_name = '';
	protected static $template_name = '';
	protected static $module_name = '';

	////////////////

	public static function set($key, $value) {
		self::$vars[$key] = $value;
	}

	public static function addTitle($title) {
		self::$titles[] = $title;
	}

	public static function addExternalStyle($style) {
		if (!in_array($style, self::$externalStyles))
			self::$externalStyles[] = $style;
		else
			user_error('External style "' . $style . '" already added', NOTICE);
	}

	public static function addExternalScript($script) {
		if (!in_array($script, self::$externalScripts['header']))
			self::$externalScripts['header'][] = $script;
		else
			user_error('External script "' . $script . '" already added', NOTICE);
	}

	public static function addDeferredExternalScript($script) {
		if (!in_array($script, self::$externalScripts['header']) && !in_array($script, self::$externalScripts['footer']))
			self::$externalScripts['footer'][] = $script;
		else
			user_error('External deferred script "' . $script . '" already added', NOTICE);
	}

	public static function clear() {
		self::$titles = array();
		self::$externalStyles = array();
		self::$styles = array();
		self::$externalScripts = array();
		self::$scripts = array();
	}

	////////////////

	public static function getLinkId()
	{
		return self::$link_id;
	}

	public static function getThemeName()
	{
		if (self::$theme_name == '')
			user_error('Theme name not set', ERROR);

		return self::$theme_name;
	}

	public static function getTemplateName()
	{
		if (self::$template_name == '')
			user_error('Template name not set', ERROR);

		return self::$template_name;
	}

	public static function getModuleName()
	{
		if (self::$module_name == '')
			user_error('Module name not set', ERROR);

		return self::$module_name;
	}

	public static function setLinkId($link_id) {
		self::$link_id = $link_id;
	}

	public static function setThemeName($theme_name) {
		self::$theme_name = $theme_name;
	}

	public static function setTemplateName($template_name) {
		self::$template_name = $template_name;
	}

	public static function setModuleName($module_name) {
		self::$module_name = $module_name;
	}
}

class Core extends Dex
{
	public static function render($_template) {
		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/core/templates/' . $_template);
	}

	public static function addStyle($style) {
		$style = 'core/resources/styles/' . $style;
		if (!in_array($style, self::$styles))
			self::$styles[] = $style;
		else
			user_error('Style "' . $style . '" already added', NOTICE);
	}

	public static function addScript($script) {
		$script = 'core/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']))
			self::$scripts['header'][] = $script;
		else
			user_error('Script "' . $script . '" already added', NOTICE);
	}

	public static function addDeferredScript($script) {
		$script = 'core/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']) && !in_array($script, self::$scripts['footer']))
			self::$scripts['footer'][] = $script;
		else
			user_error('Deferred script "' . $script . '" already added', NOTICE);
	}

	////////////////

	public static function getTitles() {
		return self::$titles;
	}

	public static function getExternalStyles() {
		return isset(self::$externalStyles) ? self::$externalStyles : array();
	}

	public static function getStyles() {
		return self::$styles;
	}

	public static function getExternalScripts($place) {
		return isset(self::$externalScripts[$place]) ? self::$externalScripts[$place] : array();
	}

	public static function getScripts($place) {
		return isset(self::$scripts[$place]) ? self::$scripts[$place] : array();
	}

	////////////////

	public static function checkModules()
	{
		$fs_modules = array();
		$handle = opendir(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/');
		while (($module_name = readdir($handle)) !== false)
			if ($module_name != '.' && $module_name != '..' && is_dir(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $module_name))
			{
				$module_file = dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $module_name . '/module.conf';
				if (is_file($module_file))
					$fs_modules[$module_name] = 1;
			}

		// check with database
		$remove_modules = array();
		$db_modules = Db::query("SELECT module_name FROM module;");
		while ($db_module = $db_modules->fetch())
			if (isset($fs_modules[$db_module['module_name']])) // file exists and the db entry too
				unset($fs_modules[$db_module['module_name']]);
			else // file does not exist but db entry does
				$remove_modules[] = $db_module['module_name'];

		// must be done outside of SELECT query to prevent database locking
		foreach ($remove_modules as $module_name) // remove module table, link_module relations of the module, module entry
		{
			user_error('Module with module_name "' . $db_module['module_name'] . '" doesn\'t exist in the filesystem and is removed from the database', NOTICE);

			Db::exec("BEGIN;
				DROP TABLE IF EXISTS module_" . Db::escape($module_name) . ";
				DELETE FROM link_module WHERE module_name = '" . Db::escape($module_name) . "';
				DELETE FROM module WHERE module_name = '" . Db::escape($module_name) . "';
			COMMIT;");
		}

		foreach ($fs_modules as $module_name => $enabled) // file exists but db entry does not
		{
			include_once(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $module_name . '/admin/setup.php');

			user_error('Module with module_name "' . $module_name . '" is inserted into the database', NOTICE);

			Db::exec("
			INSERT INTO module (module_name, enabled) VALUES (
				'" . Db::escape($module_name) . "',
				1
			);");
		}
	}

	public static function verifyLinkUrl($url, $link_id = 0)
	{
		if (!Common::validUrl($url))
			return __('Must be valid URL');

		if (Db::singleQuery("SELECT * FROM link WHERE url = '" . Db::escape($url) . "' AND link_id != '" . Db::escape($link_id) . "' LIMIT 1;"))
			return __('Already used');

		$url_base = substr($url, 0, strpos($url, '/') + 1);
		if ($url_base == 'admin/' || $url_base == 'res/' || $url_base == 'api/')
			return __('Cannot start with %s', '"' . $url_base . '"');

		return true;
	}
}

class Module extends Dex
{
	public static function render($_template) {
		if (self::$module_name == '')
			user_error('Module name not set', ERROR);

		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . self::$module_name . '/templates/' . $_template);
	}

	public static function addStyle($style) {
		if (self::$module_name == '')
			user_error('Module name not set', ERROR);

		$style = 'modules/' . self::$module_name . '/resources/styles/' . $style;
		if (!in_array($style, self::$styles))
			self::$styles[] = $style;
		else
			user_error('Style "' . $style . '" already added', NOTICE);
	}

	public static function addScript($script) {
		if (self::$module_name == '')
			user_error('Module name not set', ERROR);

		$script = 'modules/' . self::$module_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']))
			self::$scripts['header'][] = $script;
		else
			user_error('Script "' . $script . '" already added', NOTICE);
	}

	public static function addDeferredScript($script) {
		if (self::$module_name == '')
			user_error('Module name not set', ERROR);

		$script = 'modules/' . self::$module_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']) && !in_array($script, self::$scripts['footer']))
			self::$scripts['footer'][] = $script;
		else
			user_error('Deferred script "' . $script . '" already added', NOTICE);
	}
}

class Theme extends Dex
{
	public static function render($_template) {
		if (self::$theme_name == '')
			user_error('Theme name not set', ERROR);

		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/themes/' . self::$theme_name . '/templates/' . $_template);
	}

	public static function addStyle($style) {
		if (self::$theme_name == '')
			user_error('Theme name not set', ERROR);

		$style = 'themes/' . self::$theme_name . '/resources/styles/' . $style;
		if (!in_array($style, self::$styles))
			self::$styles[] = $style;
		else
			user_error('Style "' . $style . '" already added', NOTICE);
	}

	public static function addScript($script) {
		if (self::$theme_name == '')
			user_error('Theme name not set', ERROR);

		$script = 'themes/' . self::$theme_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']))
			self::$scripts['header'][] = $script;
		else
			user_error('Script "' . $script . '" already added', NOTICE);
	}

	public static function addDeferredScript($script) {
		if (self::$theme_name == '')
			user_error('Theme name not set', ERROR);

		$script = 'themes/' . self::$theme_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']) && !in_array($script, self::$scripts['footer']))
			self::$scripts['footer'][] = $script;
		else
			user_error('Deferred script "' . $script . '" already added', NOTICE);
	}
}

class Template extends Dex
{
	public static function render($_template) {
		if (self::$template_name == '')
			user_error('Template name not set', ERROR);

		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . self::$template_name . '/templates/' . $_template);
	}

	public static function addStyle($style) {
		if (self::$template_name == '')
			user_error('Template name not set', ERROR);

		$style = 'templates/' . self::$template_name . '/resources/styles/' . $style;
		if (!in_array($style, self::$styles))
			self::$styles[] = $style;
		else
			user_error('Style "' . $style . '" already added', NOTICE);
	}

	public static function addScript($script) {
		if (self::$template_name == '')
			user_error('Template name not set', ERROR);

		$script = 'templates/' . self::$template_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']))
			self::$scripts['header'][] = $script;
		else
			user_error('Script "' . $script . '" already added', NOTICE);
	}

	public static function addDeferredScript($script) {
		if (self::$template_name == '')
			user_error('Template name not set', ERROR);

		$script = 'templates/' . self::$template_name . '/resources/scripts/' . $script;
		if (!in_array($script, self::$scripts['header']) && !in_array($script, self::$scripts['footer']))
			self::$scripts['footer'][] = $script;
		else
			user_error('Deferred script "' . $script . '" already added', NOTICE);
	}
}
