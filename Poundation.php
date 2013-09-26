<?php

namespace Poundation;

spl_autoload_register(function ($className) {

	$path = dirname(dirname(__FILE__));

	$namespace = "Poundation\\";
	if (substr($className, 0, strlen($namespace)) !== $namespace) {
		$className = $namespace . $className;
	}

	$filename = str_replace("\\", "/", $path . '/' . $className . '.php');
	if (file_exists($filename)) {
		include_once($filename);
	}

});

/**
 * Creates a new Poundation String object.
 *
 * @param string $plainString
 *
 * @return \Poundation\PString
 */

function __($plainString = '')
{
	return new \Poundation\PString($plainString);
}


/**
 * Creates a new Poundation String object.
 *
 * @param string $plainString
 *
 * @return \Poundation\PString
 */
if (!function_exists('_s')) {
	function _s($plainString = '')
	{
		return new \Poundation\PString($plainString);
	}
}
?>
