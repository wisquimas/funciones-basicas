<?php
/**
 *
 */

namespace gafa;

/**
 * Class HttpUtil
 * Util functions to work with http.
 */
class HttpUtil {

    /**
     * Starts a download by sending headers. Make sure to call this method before echoing any content back to the client.
     * @param string $filePath path to the file to download.
     */
    public static function StartDownload($filePath){
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
    }
}