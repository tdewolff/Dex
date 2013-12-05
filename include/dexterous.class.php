<?php

class Dexterous
{
	public static $vars = array();
	public static $titles = array();
    public static $externalStyles = array();
	public static $styles = array();
    public static $externalScripts = array();
	public static $scripts = array();

	public static $link_id = 0;
    public static $template_name = '';

	////////////////

	public static function assign($key, $value) {
		self::$vars[$key] = $value;
	}

	public static function addTitle($title) {
		self::$titles[] = $title;
	}

    public static function addExternalStyle($style) {
        self::$externalStyles[] = $style;
    }

    public static function addExternalScript($script) {
        self::$externalScripts['header'][] = $script;
    }

    public static function addExternalDeferredScript($script) {
        self::$externalScripts['footer'][] = $script;
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

    public static function getTemplateName()
    {
        if (self::$template_name == '')
            user_error('Template name not set', ERROR);

        return self::$template_name;
    }
}

class Core extends Dexterous
{
	public static function render($_template) {
		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/core/templates/' . $_template);
	}

    public static function renderTemplate() {
        if (self::$template_name == '')
            user_error('Template name not set', ERROR);

        $_ = self::$vars;
        include(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . self::$template_name . '/template.php');
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
		global $db;

		$fs_modules = array();
		$handle = opendir(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/');
		while (($module_name = readdir($handle)) !== false)
			if (is_dir(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $module_name) && $module_name != '.' && $module_name != '..')
			{
				$module_file = dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $module_name . '/config.ini';
				if (is_file($module_file))
					$fs_modules[$module_name] = 1;
			}

		// check with database
		$db_modules = $db->query("SELECT * FROM module;");
		while ($db_module = $db_modules->fetch())
			if (isset($fs_modules[$db_module['module_name']])) // file exists and the db entry too
				unset($fs_modules[$db_module['module_name']]);
			else // file does not exist but db entry does
			{
				Log::notice('module with module_name "' . $db_module['module_name'] . '" doesn\'t exist in the filesystem and is removed from the database');

                // remove module table, link_module relations of the module, module entry
				$db->exec("
                DROP TABLE IF EXISTS module_" . $db->escape($db_module['module_name']) . ";
                DELETE FROM link_module WHERE module_name = '" . $db->escape($db_module['module_name']) . "';
                DELETE FROM module WHERE module_name = '" . $db->escape($db_module['module_name']) . "';");
			}

		foreach ($fs_modules as $name => $enabled) // file exists but db entry does not
		{
			include_once(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . $name . '/admin/setup.php');

            Log::notice('module with module_name "' . $name . '" is inserted into the database');
            $db->exec("
            INSERT INTO module (module_name, enabled) VALUES (
            	'" . $db->escape($name) . "',
            	1
            );");
		}
	}

    public static function verifyLinkUrl($url, $link_id = 0)
    {
        global $db;

        if (!Common::validUrl($url))
            return 'Must be valid URL';

        if ($db->querySingle("SELECT * FROM link WHERE url = '" . $db->escape($url) . "' AND link_id != '" . $db->escape($link_id) . "' LIMIT 1;"))
            return 'Already used';

        $url_base = substr($url, 0, strpos($url, '/') + 1);
        if ($url_base == 'admin/' || $url_base == 'res/' || $url_base == 'api/')
            return 'Cannot start with "' . $url_base . '"';

        return true;
    }
}

class Module extends Dexterous
{
	public static $module_name = '';

	public static function set($module_name) {
		self::$module_name = $module_name;
	}

	public static function render($_template) {
    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/modules/' . self::$module_name . '/templates/' . $_template);
	}

	public static function addStyle($style) {
    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		self::$styles[] = 'modules/' . self::$module_name . '/resources/styles/' . $style;
	}

	public static function addScript($script) {
    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		self::$scripts['header'][] = 'modules/' . self::$module_name . '/resources/scripts/' . $script;
	}

	public static function addDeferredScript($script) {
    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		self::$scripts['footer'][] = 'modules/' . self::$module_name . '/resources/scripts/' . $script;
	}
}

class Theme extends Dexterous
{
	public static $theme_name = '';

	public static function set($theme_name) {
		self::$theme_name = $theme_name;
	}

	public static function render($_template) {
    	if (self::$theme_name == '')
    		user_error('Theme name not set', ERROR);

		$_ = self::$vars;
		include(dirname($_SERVER['SCRIPT_FILENAME']) . '/themes/' . self::$theme_name . '/templates/' . $_template);
	}

	public static function addStyle($style) {
    	if (self::$theme_name == '')
    		user_error('Theme name not set', ERROR);

		self::$styles[] = 'themes/' . self::$theme_name . '/resources/styles/' . $style;
	}

	public static function addScript($script) {
    	if (self::$theme_name == '')
    		user_error('Theme name not set', ERROR);

		self::$scripts['header'][] = 'themes/' . self::$theme_name . '/resources/scripts/' . $script;
	}

	public static function addDeferredScript($script) {
    	if (self::$theme_name == '')
    		user_error('Theme name not set', ERROR);

		self::$scripts['footer'][] = 'themes/' . self::$theme_name . '/resources/scripts/' . $script;
	}
}

?>
