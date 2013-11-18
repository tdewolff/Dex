<?php

class Dexterous
{
	public static $vars = array();
	public static $titles = array();
	public static $styles = array();
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

    ////////////////

    public static function getLinkId()
    {
        if (self::$link_id == '')
            user_error('Link ID not set', ERROR);

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
		include('core/templates/' . $_template);
	}

    public static function renderTemplate() {
        if (self::$template_name == '')
            user_error('Template name not set', ERROR);

        $_ = self::$vars;
        include('templates/' . self::$template_name . '/template.php');
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

	////////////////

	public static function checkModules()
	{
		global $db;

		$fs_modules = array();
		$handle = opendir('modules/');
		while (($module_name = readdir($handle)) !== false)
			if (is_dir('modules/' . $module_name) && $module_name != '.' && $module_name != '..')
			{
				$module_file = 'modules/' . $module_name . '/config.ini';
				if (file_exists($module_file) !== false)
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
			include_once('modules/' . $name . '/admin/setup.php');

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
        if ($url_base == 'admin/' || $url_base == 'res/')
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
		include('modules/' . self::$module_name . '/templates/' . $_template);
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

	////////////////

	/* TODO: remove when really sure
    public static function verifyLinkUrl($url, $link_id = 0)
	{
		global $db;

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

        if (!Common::validUrl($url))
        	return 'Must be valid URL';

		if ($db->querySingle("
			SELECT * FROM link WHERE url = '" . $db->escape($url) . "' AND link_id IN (
				SELECT link_id FROM link_module WHERE module_name = '" . $db->escape(self::$module_name) . "'
			)" . ($link_id != 0 ? " AND link_id != '" . $db->escape($link_id) . "'" : "") . " LIMIT 1;"))
            return 'Already used';

        $url_base = substr($url, 0, strpos($url, '/') + 1);
        if ($url_base == 'admin/' || $url_base == 'res/')
            return 'Cannot start with "' . $url_base . '"';

        return true;
    }

    public static function getLink($url, $title = false)
    {
		global $db;

    	$link = $db->querySingle("
    	SELECT * FROM link WHERE url = '" . $db->escape($url) . "' LIMIT 1");
        if ($link)
        {
        	if ($title !== false && $title != $link['title'])
            	$db->exec("
            	UPDATE link SET
            		title = '" . $db->escape($title) . "'
            	WHERE link_id = '" . $db->escape($link['link_id']) . "';");
        	return $link['link_id'];
        }
        else
        {
            $db->exec("
            INSERT INTO link (url, title) VALUES (
                '" . $db->escape($url) . "',
                '" . $db->escape($title) . "'
            );");
            return $db->last_id();
        }
    }

    public static function updateLink($link_id, $url, $title = false)
    {
		global $db;

    	$db->exec("
    	UPDATE link SET
    		url = '" . $db->escape($url) . ($title !== false ? "',
    		title = '" . $db->escape($title) : '') . "'
    	WHERE link_id = '" . $db->escape($link_id) . "';");
	}

	public static function attachToLink($link_id)
	{
		global $db;

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		$db->exec("
        INSERT INTO link_module (link_id, module_name) VALUES (
            '" . $db->escape($link_id) . "',
            '" . $db->escape(self::$module_name) . "'
        );");
        return $db->last_id();
	}

	public static function attachToAllLinks()
	{
		global $db;

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		$db->exec("
        INSERT INTO link_module (link_id, module_name) VALUES (
            '0',
            '" . $db->escape(self::$module_name) . "'
        );");
        return $db->last_id();
	}

	public static function detachFromLink($link_module_id)
	{
		global $db;

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		$db->exec("
        DELETE FROM link_module WHERE link_module_id = '" . $db->escape($link_module_id) . "' AND module_name = '" . $db->escape(self::$module_name) . "';
        DELETE FROM link WHERE NOT EXISTS (SELECT 1 FROM link_module WHERE link.link_id = link_module.link_id);"); // remove dead links
	}

	public static function detachFromAllLinks()
	{
		global $db;

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		$db->exec("
        DELETE FROM link_module WHERE module_name = '" . $db->escape(self::$module_name) . "';");
	}

	public static function getLinkData()
	{
		global $db;

    	if (self::$link_id == '')
    		user_error('Link ID not set', ERROR);

    	if (self::$module_name == '')
    		user_error('Module name not set', ERROR);

		return $db->querySingle("
		SELECT * FROM link_module
        JOIN link ON link_module.link_id = link.link_id
        WHERE link_module.module_name = '" . $db->escape(self::$module_name) . "' AND link.link_id = '" . $db->escape(self::$link_id) . "' LIMIT 1;");
	}

	public static function getAttachedLinkData($link_module_id)
	{
		global $db;

		return $db->querySingle("
		SELECT * FROM link_module
        JOIN link ON link_module.link_id = link.link_id
        WHERE link_module.link_module_id = '" . $db->escape($link_module_id) . "' LIMIT 1;");
	}*/
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
		include('themes/' . self::$theme_name . '/templates/' . $_template);
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
