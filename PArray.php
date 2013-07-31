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
     * @return PArray
	 */
	function add($object, $index = -1) {
        if (is_integer($index) && $index >= 0) {
		    $this->map[$index] = $object;
        } else {
            $this->map[] = $object;
        }
        return $this;
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
		if (is_null($offset)) {
		    $this->add($value);
		} else if (is_integer($offset) && $offset >= 0) {
            $this->add($value, $offset);
        } else {
			throw new \Exception('Array cannot handle a key.',100,null);
		}
	}

	/**
	 * Sorts the array according to the sort descriptor.
	 * @param PSortDescriptor $descriptor
	 *
	 * @return bool
	 */
	public function sortUsingSortDescriptor(PSortDescriptor $descriptor)  {
		return usort($this->map, array($descriptor, 'cmpObjectsByDescriptor'));
	}

	/**
	 * Creates a new array sorted according to the sort descriptor.
	 * @param PSortDescriptor $descriptor
	 *
	 * @return null|PArray
	 */
	public function getSortedArrayUsingSortDescriptor(PSortDescriptor $descriptor) {

		$array = self::create($this->map);
		if ($array->sortUsingSortDescriptor($descriptor)) {
			return $array;
		} else {
			return null;
		}

	}

	public function sortByCallback($callback) {
		return usort($this->map, $callback);
	}
}

?>