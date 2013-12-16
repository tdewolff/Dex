<?php
/**
 * Description: Compresses images using Smush.it
 *
 * @license MIT
 * @author  Mathew Davies <thepixeldeveloper@googlemail.com>
 */
class SmushIt
{
    const user_agent = 'Smush.it PHP Class (+http://mathew-davies.co.uk)';

    /**
     * Smush.it request URL
     */
    const url = 'http://www.smushit.com/ysmush.it/ws.php';

    /**
     * Compress image using smush.it. Image must be available online
     *
     * @param  string url to image.
     * @throws Smush_exception
     * @return object
     *
     *  src       = source location of input image
     *  src_size  = size of the source image in bytes
     *  dest      = temporary location of the compressed image
     *  dest_size = size of compressed image in bytes
     *  percent   = how much smaller the compressed image is
     */
    public static function compress($image) {
        $allowUrlFopen = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
        if ($allowUrlFopen) {
            $contents = file_get_contents(self::url.'?'.http_build_query( array('img' => $image ) ), false, stream_context_create(array(
                'http' => array(
                    'method' => 'GET',
                    'max_redirects' => 0,
                    'timeout' => 5,
                )
            )));
        } elseif (extension_loaded( 'curl' )) {
            $ch = curl_init(self::url.'?'.http_build_query( array('img' => $image ) ));
            curl_setopt($ch, CURLOPT_USERAGENT, self::user_agent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $contents = curl_exec($ch);
            curl_close($ch);
        } else {
            throw new Exception(
               "Could not make HTTP request: allow_url_open is false and cURL not available"
            );
        }
        if (false === $contents) {
            throw new Exception(
               "No HTTP response from server"
            );
        }

        // JSON response
        $result = json_decode(trim($contents));
        if ( isset ( $result->error ) )
            throw new Exception($result->error);

        $result->dest = urldecode($result->dest);
        return $result;
    }
}
?>