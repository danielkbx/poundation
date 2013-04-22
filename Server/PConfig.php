<?php

namespace Poundation\Server;

class PConfig
{

	/**
	 * Returns true if cURL is enabled.
	 *
	 * @return bool
	 */

	static function isCurlEnabled()
	{
		return function_exists('curl_version');
	}

	/**
	 * Returns true if allow_url_fopen is enabled.
	 *
	 * @return bool
	 */
	static function isURLOpeningAllowed()
	{
		return (bool)ini_get('allow_url_fopen');
	}
}
