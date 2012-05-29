<?php

namespace Poundation;

require_once ('PCollection.php');
require_once ('PArray.php');

use Poundation\PCollection;

/**
 *
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
		return PArray::arrayWithArray(array_values($this->map));
	}
	
	/**
	 * Returns an array with all keys of the dictionary.
	 * @return PArray
	 */
	public function allKeys() {
		return PArray::arrayWithArray(array_keys($this->map));
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

?>