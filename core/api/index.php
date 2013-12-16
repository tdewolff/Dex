<?php

require_once('include/console.class.php');

if (API::action('status'))
{
    if (Console::hasOutput())
        API::set('status', Console::getOutput());
    API::finish();
}

session_start();
if (!Session::isAdmin())
    user_error('Forbidden access', ERROR);

if (API::action('optimize_site'))
{
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
                    $script_directories[] = $extension_directory . $module_name . '/resources/scripts/';
                if (is_dir($extension_directory . $module_name . '/resources/styles/'))
                    $style_directories[] = $extension_directory . $module_name . '/resources/styles/';
            }
    }

    Console::appendLine('---- Compressing JS files ----');
    $hasCompressed = false;
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
                $hasCompressed = true;
                Console::append($script_name . '... ');
                $input = file_get_contents($script_name);
                try {
                    $output = ClosureCompiler::minify($input);
                    file_put_contents($script_min_name, $output);
                } catch (Exception $e) {
                    $error = strlen($e->getMessage()) ? $e->getMessage() : 'Unknown error';
                    user_error($error, WARNING);
                    Console::appendLine('failed: ' . $error);
                    continue;
                }
                Console::appendLine('done (ratio ' . ($info->getSize() ? number_format((float) strlen($output) / $info->getSize() * 100.0, 1) : 0) . '%)');
            }
        }
    }
    if (!$hasCompressed)
        Console::appendLine('Nothing to be done');

    Console::appendLine('');
    Console::appendLine('---- Compressing CSS files ----');
    $hasCompressed = false;
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
                $hasCompressed = true;
                Console::append($style_name . '... ');
                $input = file_get_contents($style_name);
                $output = CssCompressor::process($input);
                file_put_contents($style_min_name, $output);
                Console::appendLine('done (ratio ' . ($info->getSize() ? number_format((float) strlen($output) / $info->getSize() * 100.0, 1) : 0) . '%)');
            }
        }
    }
    if (!$hasCompressed)
        Console::appendLine('Nothing to be done');

    Console::appendLine('');
    Console::appendLine('---- Compressing image files ----');
    $hasCompressed = false;
    $root = new RecursiveDirectoryIterator('assets/');
    foreach (new RecursiveIteratorIterator($root) as $image_name => $info)
    {
        $image_name = str_replace('\\', '/', $image_name);
        $image_min_name = Common::insertMinExtension($image_name);
        if ($info->isFile() && $info->getSize() && !Common::hasMinExtension($image_name) &&
            Resource::isImage(substr($image_name, strrpos($image_name, '.') + 1)) && (
            !is_file($image_min_name) ||
            $info->getMTime() > filemtime($image_min_name)))
        {
            $hasCompressed = true;
            Console::append($image_name . '... ');
            Console::appendLine(Common::fullBaseUrl() . $base_url . 'res/' . $image_name);
            try {
                $output_info = SmushIt::compress(Common::fullBaseUrl() . $base_url . 'res/' . $image_name);
            } catch (Exception $e) {
                $error = strlen($e->getMessage()) ? $e->getMessage() : 'Unknown error';
                user_error($error, WARNING);
                Console::appendLine('failed: ' . $error);
                continue;
            }

            $output = file_get_contents($output_info->dest);
            file_put_contents($image_min_name, $output);
            Console::appendLine('done (ratio ' . ($info->getSize() ? number_format((float) $output_info->dest_size / $info->getSize() * 100.0, 1) : 0) . '%)');
        }
    }
    if (!$hasCompressed)
        Console::appendLine('Nothing to be done');

    Console::finish();
    API::finish();
}
else if (API::action('clear_logs'))
{
    Log::close();

    $handle = opendir('logs/');
    while (($log_name = readdir($handle)) !== false)
        if (is_file('logs/' . $log_name))
            unlink('logs/' . $log_name);

    Log::open();
    API::finish();
}
else if (API::action('clear_cache'))
{
    $handle = opendir('cache/');
    while (($cache_name = readdir($handle)) !== false)
        if (is_file('cache/' . $cache_name))
            unlink('cache/' . $cache_name);
    API::finish();
}
else if (API::action('get_logs'))
{
    require_once('include/file.php');

    $lines = API::has('lines') ? API::get('lines') : 100;

    $logs = array();
    $logfile = array_reverse(Log::getLastLines($lines));
    $oldDatetime = false;
    foreach ($logfile as $logline)
    {
        $logline = explode(' ', $logline);

        $datetime = new DateTime(substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1));
        if (!$oldDatetime)
            $oldDatetime = $datetime;
        else if ($oldDatetime->diff($datetime)->s > 1)
        {
            $logs[] = array(
                'datetime' => '',
                'ipaddress' => '',
                'type' => '',
                'message' => ''
            );
            $oldDatetime = $datetime;
        }

        $logs[] = array(
            'datetime' => substr($logline[0], 1) . ' ' . substr($logline[1], 0, -1),
            'ipaddress' => $logline[2],
            'type' => $logline[3],
            'message' => htmlentities(implode(' ', array_slice($logline, 4)))
        );
    }
    API::set('logs', $logs);
    API::finish();
}

?>