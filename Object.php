<?php

namespace Poundation;

/*
 * Object is the superclass of all poundation objects.
 * @author danielkbx
 */
class Object {
	
	function isEqual($otherObject) {
		return ($otherObject === $this);
	}
	
}

?>