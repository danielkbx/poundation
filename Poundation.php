<?php

/*
 * First, all class files are included.
 */
require_once 'String.php';
require_once 'Set.php';

/*
 * Finally, we declare a bunch of little factory methods.
 */

/*
 * Creates a new String object.
 * @param String plainstring
 * @return Poundation\String
 */
function __($plainString='') {
    return new Poundation\String($plainString);
}

?>
