<?php

use Poundation\PArray;

require_once 'PObject.php';
require_once 'PError.php';

require_once 'PString.php';
require_once 'PCharacterSet.php';

require_once 'PSet.php';
require_once 'PDictionary.php';
require_once 'PArray.php';

require_once 'PURL.php';
require_once 'PMailAddress.php';
require_once 'Server/PRequest.php';

require_once 'PImage.php';

require_once 'PCoordinate.php';
require_once 'PAddress.php';

/**
 * Creates a new Poundation String object.
 * @param string $plainString
 * @return \Poundation\PString
 */
function __($plainString='') {
	return new \Poundation\PString($plainString);
}

/**
 * Returns a new Poundation Array object.
 * @param array $array
 * @return \Poundation\PArray
 */
function parray($array=false) {
	return PArray::create($array);
}

?>
