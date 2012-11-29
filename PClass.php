<?php

namespace Poundation;

require_once ('PObject.php');
require_once ('PString.php');

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
			return self::classFromString($objectsClassName);
		} else {
			throw new \Exception(@"Cannot create class class from non-object value",101,null);
		}
	}
	
	static public function classFromString($name) {
		if ($name && (is_string($name) || $name instanceof PString)) {
			$existingClass = (isset(PClass::$_staticMap[$name])) ? PClass::$_staticMap[$name] : null;
			if (!$existingClass) {
				$existingClass = new PClass($name);
				PClass::$_staticMap[$name] = $existingClass;
			}
			return $existingClass;
		} else {
			throw new \Exception(@"Cannot create class class from non-string value",101,null);
		}
	}

	private function __construct($name) {
		if ($name && (is_string($name) || $name instanceof PString)) {
			$this->_name = $name;
			$parentClassname = get_parent_class($this->_name);
			while($parentClassname) {
				$this->_classHirarchy[] = $parentClassname;
				$parentClassname = get_parent_class($parentClassname);
			}
			
			$interfaces = class_implements($this->_name);
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
	 * @return PString
	 */
	function name() {
		return PString::createFromString($this->_name);	
	}
	
	/**
	 * Determines if the class inherits from the given class or is the given class.
	 * @param PClass $class
	 * @throws \Exception
	 * @return boolean
	 */
	function isKindOfClass($class) {
		
		$classname = false;
		if (is_string($class)) {
			$classname = $class;
		} else if ($class instanceof PString) {
			$classname = (string)$class;
		} else if ($class instanceof PClass) {
			$classname = (string)$class->name();
		} else if (is_object($class)) {
			$classname = get_class($class);
		}
		
		if ($classname !== false) {
			if ($this->_name == $classname) {
				return true;
			} else {
				return (array_search($classname,$this->_classHirarchy) !== false);
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
	
	/**
	 * Invokes the method on the class (therefore it is a class method call) and returns the result.
	 * @param string $methodName
	 */
	function invokeMethod($methodName) {
		return $this->invokeMethodWithParameters($methodName, array());
	}
	
	/**
	 * Invokes the method on the class (therefore it is a class method call), passes the arguments and returns the result.
	 * @param string $methodName
	 * @param array $params
	 * @return mixed
	 */
	function invokeMethodWithParameters($methodName,$params) {
		if (is_string($methodName)) {
			return call_user_func_array(array($this->_name,$methodName), $params);
		}
	}
	
}

?>