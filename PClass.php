<?php

namespace Poundation;

require_once ('PObject.php');

use Poundation\PObject;

/**
 * @abstract PClass is a helper class to handle classes.
 * @author danielkbx
 *        
 */
class PClass extends PObject {
	static private $_staticMap = array();	
	
	private $_name;
	private $_classHirarchy = array(); 
	private $_classInterfaces = array();
	
	/**
	 * Returns a class object matching the given object.
	 * @param $object
	 * @throws \Exception
	 * @return \Poundation\PClass
	 */
	static public function classFromObject($object) {
		if ($object && is_object($object)) {
			$objectsClassName = get_class($object);
			$existingClass = (isset(PClass::$_staticMap[$objectsClassName])) ? PClass::$_staticMap[$objectsClassName] : null;
			if (!$existingClass) {
				$existingClass = new PClass();
				if ($existingClass->__constructWithObject($object)) {
					PClass::$_staticMap[$objectsClassName] = $existingClass;
				} else {
					throw new \Exception('Cannot create class class from value ' . $object,102,null);	
				}
			}
			return $existingClass;
		} else {
			throw new \Exception(@"Cannot create class class from non-object value",101,null);
		}
	}
	
	private function __constructWithObject($object) {
		if ($object && is_object($object)) {
			$this->_name = get_class($object);
			
			$parentClassname = get_parent_class($object);
			while($parentClassname) {
				$this->_classHirarchy[] = $parentClassname;
				$parentClassname = get_parent_class($parentClassname);
			}
			
			$interfaces = class_implements($object);
			foreach ($interfaces as $interface) {
				$this->_classInterfaces[] = $interface;
			}
			
			return $this;
		} else {
			return false;
		}	
	}
	
	/**
	 * Returns the name of the class.
	 * @return string
	 */
	function name() {
		return $this->_name;	
	}
	
	/**
	 * Determines if the class inherits from the given class or is the given class.
	 * @param PClass $class
	 * @throws \Exception
	 * @return boolean
	 */
	function isKindOfClass($class) {
		if (is_object($class)) {
			if (get_class($class) == 'PClass')
			{
				$class = $class->name();
			}
		}
		if (is_string($class)) {
			if ($this->name() == $class) {
				return true;
			} else {
				return (array_search($class,$this->_classHirarchy) !== false);
			}
		} else {
			throw new \Exception('Cannot check class with target value ' . $class,104,null);
		}
	}
	
	/**
	 * Determines if a class implements the given interface.
	 * @param string $interface
	 * @return boolean
	 */
	function implementsInterface($interface) {
		if ($interface) {
			return (array_search($interface, $this->_classInterfaces) !== false);
		}
		return false;
	}
	
}

?>