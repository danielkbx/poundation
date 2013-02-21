<?php

namespace Poundation;

require_once 'PCollection.php';
require_once 'PString.php';

use Poundation\PCollection;

/**
 * @abstract PArray manages a collection of objects identified by an index value. It is comparable to PHP's numeric array. 
 * @author danielkbx
 */
class PArray extends PCollection {
	
	static function create($array=NULL) {
		$newArray = new PArray();
		if ($array) {
			$newArray->addArray($array);
		}
		return $newArray;
	}
	
	static function createProgressivArray($startValue,$endValue,$step = 1) {
		$newArray = new PArray();
		if (is_numeric($startValue) && is_numeric($endValue) && is_numeric($step)) {
			if ($startValue < $endValue && $step > 0) {
				for ($i = $startValue; $i <= $endValue; $i = $i+$step) {
					$newArray->add($i);
				}
			} else if ($startValue > $endValue && $step < 0) {
				for ($i = $startValue; $i >= $endValue; $i = $i+$step) {
					$newArray->add($i);
				}
			}
		}
		return $newArray;
	}
	
	/**
	 * Adds an object to the collection.
	 * @param $object
	 */
	function add($object) {
		$this->map[] = $object;
	}
	
	function addArray($array) {
		if ($array) {
			$process = is_array($array);
			if ($process == false) {
				if (is_object($array)) {
					$class = PClass::classFromObject($array);
					$process = $class->implementsInterface('Traversable');
				}	
			}
			
			if ($process) {
				foreach($array as $value) {
					$this->add($value);
				}
			} else {
				$exceptionString = __('Cannot add values from type ');
				if (is_object($array)) {
					$exceptionString->addString(get_class($array));
				} else {
					$exceptionString->addString(gettype($array));
				}
				throw new \Exception($exceptionString,100,null);
			}
		} else {
			
		}
	}
	
	function dictionary() {
		$dict = new PDictionary();
		foreach($this as $value) {
			$dict->setValueForKey($value, $value);
		}
		return $dict;
	}

	
	/*
	 * (non-PHPdoc)
	 * @see \Poundation\Collection::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if ($offset == null) {
			$this->add($value);
		} else {
			throw new \Exception('Array cannot handle a key.',100,null);
		}
	}
}

?>