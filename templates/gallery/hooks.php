<?php

Hooks::attach('main', 0, function () {
	Core::addStyle('vendor/fancybox.css');
	Core::addDeferredScript('vendor/jquery.fancybox.min.js');

	$link_id = Core::getLinkId();
	$content = Db::singleQuery("SELECT content FROM content WHERE link_id = '" . Db::escape($link_id) . "' AND name = 'settings' LIMIT 1;");
	if ($content)
	{
		$settings = json_decode($content['content'], true);
		$dir = $settings['directory'] . '/';
		$max_width = 200;

		$images = array();
		$handle = opendir($dir);
		while (($name = readdir($handle)) !== false)
		{
			if (is_file($dir . $name) && !Common::hasMinExtension($name))
			{
				$filename = $name;
				if (is_file($dir . Common::insertMinExtension($filename)) && filemtime($dir . Common::insertMinExtension($filename)) > filemtime($dir . $filename))
					$filename = Common::insertMinExtension($filename);

				$last_slash = strrpos($name, '/');
				$title = substr($name, $last_slash ? $last_slash + 1 : 0, strrpos($name, '.'));
				$extension = substr($name, strrpos($name, '.') + 1);

				if (Resource::isImage($extension))
				{
					list($width, $height, $type, $attribute) = getimagesize($dir . $name);
					$images[] = array(
						'url' => $dir . $filename,
						'name' => $name,
						'title' => (strlen($title) > 40 ? substr($title, 0, 40) > '&mdash;' : $title) . '.' . $extension,
						'width' => $width,
						'attr' => Resource::imageSizeAttributes(explode('/', 'res/' . $dir . $filename), $max_width),
						'mtime' => filemtime($dir . $filename)
					);
				}
			}
		}
		Common::sortOn($images, 'name');
		Template::set('images', $images);
		Template::set('max_width', $max_width);
	}
	Template::render('index.tpl');
});
