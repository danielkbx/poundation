<?php
/**
 * meinERP 
 * User: Jan Fanslau
 * Date: 19.04.13
 * Time: 11:39
 */

namespace Poundation\Server;

class PConfig {

    /**
     * Returns if Server has CURL enabled
     * @return bool
     */

    static function curlEnabled()
    {
        return function_exists('curl_version');
    }

    /**
     * Returns if the Server has allow_url_fopen enabled
     * @return bool
     */
    static function allowURLFOpen() {
        return (bool) ini_get('allow_url_fopen');
    }
}
