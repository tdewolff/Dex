<?php

class Resource
{
    private static $caching = false;

    private static $extensions_mime = array(
        'js' => 'application/x-javascript',
        'css' => 'text/css',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'htm' => 'text/html',
        'html' => 'text/html',
        'svg' => 'image/svg+xml',
        'eot' => 'application/vnd.ms-fontobject',
        'woff' => 'application/font-woff',
        'otf' => 'application/octet-stream',
        'ttf' => 'application/x-font-ttf'
    );

    public static function setCaching($caching) {
        self::$caching = $caching;
    }

    public static function getMime($extension)
    {
        return self::$extensions_mime[strtolower($extension)];
    }

    public static function isResource($extension)
    {
        return array_key_exists(strtolower($extension), self::$extensions_mime);
    }

    public static function isImage($extension)
    {
        $extension = strtolower($extension);
        return ($extension == 'png' || $extension == 'gif' || $extension == 'jpg' || $extension == 'jpeg');
    }

    private static function cacheFilename($filenames, $cache_formatted_filename)
    {
        $latest_modify_time = 0;
        foreach ($filenames as $filename)
            if (!file_exists($filename))
                user_error('File "' . $filename . '" could not be found', WARNING);
            else
            {
                $modify_time = filemtime($filename);
                $latest_modify_time = max($modify_time, $latest_modify_time);
            }

        $unique_scriptname = implode($filenames) . $latest_modify_time;
        return sprintf($cache_formatted_filename, sha1($unique_scriptname));
    }

    public static function concatenateFiles($filenames, $extension)
    {
        $starttime_local = explode(' ', microtime());
        $cache_filename = self::cacheFilename($filenames, 'cache/%s.' . $extension);
        if (!file_exists($cache_filename) || self::$caching == false)
        {
            $content = '';
            foreach ($filenames as $filename)
                if (file_exists($filename))
                    $content .= file_get_contents($filename);

            $f = fopen($cache_filename, 'w');
            fwrite($f, $content);
            fclose($f);

            $endtime_local = explode(' ', microtime());
            $totaltime = ($endtime_local[1] + $endtime_local[0] - $starttime_local[1] - $starttime_local[0]);
            Log::caching($cache_filename . ' took ' . number_format($totaltime, 4) . 's');
        }
        return 'res/' . $cache_filename;
    }

    public static function expandUrl($request_url)
    {
        $filename = '';
        $url = explode('/', $request_url);
        if (isset($url[1]) && isset($url[2]))
            if ($url[1] == 'cache')
                $filename = 'cache/' . implode('/', array_splice($url, 2));
            else if ($url[1] == 'assets')
                $filename = 'assets/' . implode('/', array_splice($url, 2));
            else if ($url[1] == 'core')
                $filename = 'core/resources/' . implode('/', array_splice($url, 2));
            else if ($url[1] == 'module')
                $filename = 'modules/' . $url[2] . '/resources/' . implode('/', array_splice($url, 3));
            else if ($url[1] == 'theme')
                $filename = 'themes/' . $url[2] . '/resources/' . implode('/', array_splice($url, 3));

        if (empty($filename))
            Log::warning('Could not expand URL "' . $request_url . '" to a resource');

        return $filename;
    }

    public static function imageSize($filename, $max_width, $max_height = 0, $scale = 0)
    {
        if (!file_exists($filename))
        {
            Log::warning('Could not find "' . $filename . '" to determine new image size');
            return array(0, 0);
        }

        list($width, $height, $mime_type, $attribute) = getimagesize($filename);

        $new_width = $width;
        $new_height = $height;
        if ($width > $max_width && $max_width != 0)
        {
            $new_width = $max_width;
            $new_height = floor($height * ($new_width / $width));
            if ($new_height > $max_height && $max_height != 0)
            {
                $new_height = $max_height;
                $new_width = floor($width * ($new_height / $height));
            }
        }
        else if ($height > $max_height && $max_height != 0)
        {
            $new_height = $max_height;
            $new_width = floor($width * ($new_height / $height));
            if ($new_width > $max_width && $max_width != 0)
            {
                $new_width = $max_width;
                $new_height = floor($height * ($new_width / $width));
            }
        }
        else if ($scale != 0)
        {
            $new_width = floor($scale * $width);
            $new_height = floor($scale * $height);
        }
        return array($new_width, $new_height);
    }

    public static function imageSizeAttributes($request_url, $max_width, $max_height = 0, $scale = 0)
    {
        $filename = self::expandUrl($request_url);
        list($new_width, $new_height) = self::imageSize($filename, $max_width, $max_height, $scale);
        return 'width="' . $new_width . '" height="' . $new_height . '"';
    }

    public static function imageResize($filename, $max_width, $max_height = 0, $scale = 0)
    {
        $starttime_local = explode(' ', microtime());
        $cache_filename = 'cache/' . sha1($filename . '_' . $max_width . '_' . $max_height . '_' . $scale . '_' . filemtime($filename)) . '.png';
        if (!file_exists($cache_filename))
        {
            list($width, $height, $mime_type, $attribute) = getimagesize($filename);
            list($new_width, $new_height) = self::imageSize($filename, $max_width, $max_height, $scale);

            switch (image_type_to_mime_type($mime_type))
            {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($filename);
                    break;

                case 'image/png':
                    $image = imagecreatefrompng($filename);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;

                case 'image/gif':
                    $image = imagecreatefromgif($filename);
                    break;

                default:
                    Log::warning('Could not resize "' . $filename . '" since it is not a JPEG, PNG or GIF');
                    return $filename;
            }

            $resizedImage = imagecreatetruecolor($new_width, $new_height);
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            if (function_exists('imageistruecolor') && !imageistruecolor($image) && imagecolortransparent($image) > 0)
                imagetruecolortopalette($resizedImage, false, imagecolorstotal($image));

            imagepng($resizedImage, $cache_filename);
            imagedestroy($resizedImage);
            imagedestroy($image);

            $endtime_local = explode(' ', microtime());
            $totaltime = ($endtime_local[1] + $endtime_local[0] - $starttime_local[1] - $starttime_local[0]);
            Log::caching($cache_filename . ' resize took ' . number_format($totaltime, 4) . 's (' . $width . 'x' . $height . ' to ' . $new_width . 'x' . $new_height . ')');
        }
        return $cache_filename;
    }
}

?>