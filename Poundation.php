<?php

/*
 * First, all class files are included.
 */
require_once 'PString.php';
require_once 'PSet.php';

/*
 * Finally, we declare a bunch of little factory methods.
 */

/*
 * Creates a new String object.
 * @param String plainstring
 * @return Poundation\PString
 */
function __($plainString='') {
    return new Poundation\PString($plainString);
}

?>
