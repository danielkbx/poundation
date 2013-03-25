<?php

namespace Poundation;

require_once ('PClass.php');

use \Poundation\PClass;

/*
 * PObject is the superclass of all poundation objects.
 * @author danielkbx
 */
class PObject {

    protected $flags;

	function isEqual($otherObject) {
		return ($otherObject === $this);
	}
	
	function __toString() {
		return get_class($this);
	}

	function classObject() {
		return PClass::classFromObject($this);
	}
	
	/**
	 * Determines of the object's class is the given class or inherits from it.
	 * @param $class
	 * @return boolean
	 */
	function isKindOfClass($class) {
		return $this->classObject()->isKindOfClass($class);
	}
	
	function implementsInterface($interface) {
		return $this->classObject()->implementsInterface($interface);
	}

    protected function isFlagSet($flag) {
        return (($this->flags & $flag) == $flag);
    }

    protected function setFlag($flag, $value = true) {
        if ($value == true) {
            $this->flags |= $flag;
        } else {
            $this->flags &= ~$flag;
        }
    }
	
}

?>