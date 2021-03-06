<?php

require_once('include/console.class.php');

if (!User::isAdmin())
{
	Common::responseCode(403);
	user_error('Forbidden access', ERROR);
}


Console::append(__('Optimizing database...'));
$database_size = Db::filesize();

/* TODO: Future implementation of versioning

// remove old content versions so that visitors get to see the latest
Db::exec("
DELETE FROM content
WHERE content_id NOT IN (
	SELECT content_id FROM content
	GROUP BY link_id, name
	ORDER BY modify_time DESC LIMIT 1
);");
*/

Db::exec("VACUUM;");
Console::appendLine(__('done') . ' (' . number_format((float) Db::filesize() / $database_size * 100.0, 1) . '%)');


require_once('vendor/closure-compiler.php');
require_once('vendor/css-compressor.php');
require_once('vendor/smush-it.php');

$script_directories = array('core/resources/scripts/');
$style_directories = array('core/resources/styles/');

$extension_directories = array('modules/', 'templates/', 'themes/');
foreach ($extension_directories as $extension_directory)
{
	$handle = opendir($extension_directory);
	while (($module_name = readdir($handle)) !== false)
		if ($module_name != '.' && $module_name != '..' &&
			is_dir($extension_directory . $module_name) &&
			is_dir($extension_directory . $module_name . '/resources/'))
		{
			if (is_dir($extension_directory . $module_name . '/resources/scripts/'))
			{
				if (is_writable($extension_directory . $module_name . '/resources/scripts/'))
					$script_directories[] = $extension_directory . $module_name . '/resources/scripts/';
				else
					Console::appendLine(__('Directory "%s" is not writable', $extension_directory . $module_name . '/resources/scripts/'));
			}

			if (is_dir($extension_directory . $module_name . '/resources/styles/'))
			{
				if (is_writable($extension_directory . $module_name . '/resources/styles/'))
					$style_directories[] = $extension_directory . $module_name . '/resources/styles/';
				else
					Console::appendLine(__('Directory "%s" is not writable', $extension_directory . $module_name . '/resources/styles/'));
			}
		}
}

foreach ($script_directories as $script_directory)
{
	$root = new RecursiveDirectoryIterator($script_directory);
	foreach (new RecursiveIteratorIterator($root) as $script_name => $info)
	{
		$script_name = str_replace('\\', '/', $script_name);
		$script_min_name = Common::insertMinExtension($script_name);
		if ($info->isFile() && $info->getSize() && !Common::hasMinExtension($script_name) && (
			!is_file($script_min_name) ||
			$info->getMTime() > filemtime($script_min_name)))
		{
			Console::append(__('Compressing \'%s\'...', $script_name));

			if (!is_readable($script_name))
			{
				Console::appendLine('failed: file unreadable');
				continue;
			}

			$input = file_get_contents($script_name);
			try {
				$output = ClosureCompiler::minify($input);

				if (file_exists($script_min_name))
					unlink($script_min_name);
				file_put_contents($script_min_name, $output);
			} catch (Exception $e) {
				$error = strlen($e->getMessage()) ? $e->getMessage() : 'unknown error';
				Console::appendLine('failed: ' . lcfirst($error));
				continue;
			}

			Console::appendLine(__('done') . ' (' . ($info->getSize() ? number_format((float) strlen($output) / $info->getSize() * 100.0, 1) : 0) . '%)');
		}
	}
}

foreach ($style_directories as $style_directory)
{
	$root = new RecursiveDirectoryIterator($style_directory);
	foreach (new RecursiveIteratorIterator($root) as $style_name => $info)
	{
		$style_name = str_replace('\\', '/', $style_name);
		$style_min_name = Common::insertMinExtension($style_name);
		if ($info->isFile() && $info->getSize() && !Common::hasMinExtension($style_name) && (
			!is_file($style_min_name) ||
			$info->getMTime() > filemtime($style_min_name)))
		{
			Console::append(__('Compressing \'%s\'...', $style_name));

			if (!is_readable($style_name))
			{
				Console::appendLine('failed: file unreadable');
				continue;
			}

			$input = file_get_contents($style_name);
			$output = CssCompressor::process($input);

			if (file_exists($style_min_name))
				unlink($style_min_name);
			file_put_contents($style_min_name, $output);

			Console::appendLine(__('done') . ' (' . ($info->getSize() ? number_format((float) strlen($output) / $info->getSize() * 100.0, 1) : 0) . '%)');
		}
	}
}

$root = new RecursiveDirectoryIterator('assets/');
foreach (new RecursiveIteratorIterator($root) as $image_name => $info)
{
	$image_name = str_replace('\\', '/', $image_name);
	$image_min_name = Common::insertMinExtension($image_name);
	$extension = substr($image_name, strrpos($image_name, '.') + 1);
	if ($info->isFile() && $info->getSize() && !Common::hasMinExtension($image_name) &&
		Resource::isImage($extension) && (!is_file($image_min_name) || $info->getMTime() > filemtime($image_min_name)))
	{
		Console::append(__('Compressing \'%s\'...', $image_name));

		if (!is_readable($image_name))
		{
			Console::appendLine('failed: file unreadable');
			continue;
		}
		else if (filesize($image_name) > 1024 * 1024)
		{
			Console::appendLine('failed: too big for SmushIt');
			continue;
		}

		$image_url = Common::fullBaseUrl() . Common::$base_url . 'res/' . $image_name;
		$contents = Common::getUrlContents('http://www.smushit.com/ysmush.it/ws.php?' . http_build_query(array('img' => $image_url)));
		if ($contents === null)
		{
			Console::appendLine('failed: could not make HTTP request to SmushIt');
			continue;
		}

		$smushit = json_decode(trim($contents));
		if (empty($smushit))
		{
			Console::appendLine('failed: no response from SmushIt');
			continue;
		}
		else if (isset($smushit->error))
		{
			Console::appendLine('failed: ' . lcfirst($smushit->error));
			continue;
		}

		// gif -> png case
		$new_extension = substr($smushit->dest, strrpos($smushit->dest, '.') + 1);
		if ($extension != $new_extension)
			$image_min_name .= '.' . $new_extension;

		if (file_exists($image_min_name))
			unlink($image_min_name);
		file_put_contents($image_min_name, Common::getUrlContents(urldecode($smushit->dest)));

		Console::appendLine(__('done') . ' (' . ($info->getSize() ? number_format((float) $smushit->dest_size / $info->getSize() * 100.0, 1) : 0) . '%)');
	}
}

if (!Console::hasOutput())
	Console::appendLine(__('Nothing to do'));
else
{
	Console::appendLine('');
	Console::appendLine(__('Finished'));
}

Console::finish();
API::finish();
