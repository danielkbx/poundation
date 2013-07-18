<?php

namespace Poundation;

require_once 'PCollection.php';
require_once 'PArray.php';

use Poundation\PCollection;

/**
 * @abstract A dictionary associates a value with a key the same way like PHP's assioative arrays. You cannot add a value without a key.
 * @author danielkbx
 *        
 */
class PDictionary extends PCollection {
	
	/**
	 * Sets a value for a key.
	 * @param multitype $value
	 * @param string $key
	 * @throws \Exception
	 */
	public function setValueForKey($value,$key) {
		if ($value == null) {
			throw new \Exception('Cannot set empty value on dictionary.',107,null);
		} else if ($key == null) {
			throw new \Exception('Cannot set value with empty key on dictionary.',108,null);
		} else {
			$this->map[$key] = $value;
		}
	}
	
	/**
	 * Removes the value with the given key.
	 * @param string $key
	 */
	public function removeValueForKey($key) {
		if (isset($this->map[$key])) {
			unset($this->map[$key]);
		}
	}

	/**
	 * Removes the given value object.
	 * @param $value
	 */
	public function removeValue($value) {
		$keyToRemove = null;
		foreach($this->map as $key => $object) {
			if ($value === $object) {
				$keyToRemove = $key;
				break;
			}
		}

		if (!is_null($keyToRemove)) {
			$this->removeValueForKey($keyToRemove);
		}
	}
	
	/**
	 * Returns the value assoziated with the given key.
	 * @param string $key
	 * @return multitype
	 */
	public function valueForKey($key) {
		return (isset($this->map[$key])) ? $this->map[$key] : null;
	}
	
	/**
	 * Returns an array with all values of the dictionary.
	 * @return PArray
	 */
	public function allValues() {
		return PArray::create(array_values($this->map));
	}
	
	/**
	 * Returns an array with all keys of the dictionary.
	 * @return PArray
	 */
	public function allKeys() {
		return parray(array_keys($this->map));
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \Poundation\PCollection::offsetSet()
	 *
	 */
	public function offsetSet($offset, $value) {
		if ($offset == null) {
			throw new \Exception('Dictionary cannot handle a value without a key.', 106, null);
		} else {
			$this->setValueForKey($value, $offset);
		}
	}
}
