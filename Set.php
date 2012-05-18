<?php

namespace Poundation;

require_once ('Collection.php');

use Poundation\Collection;

/**
 * A set manages a collection of objects.
 * A set contains every object only once.
 * 
 * @abstract A set collection
 * @author danielkbx
 */
class Set extends Collection {
	
	/*
	 * Adds an object to the set if it not part yet. 
	 * @param $object
	 * @return Poundation\Set
	 */
	function add($object) {
		if ($this->contains ( $object ) === false) {
			$this->map [] = $object;
		}
		return $this;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Poundation\Collection::offsetSet()
	 */
	public function offsetSet($offset, $value) {
		if ($offset == null) {
			$this->add($value);
		} else {
			throw new \Exception('Sets cannot have a key.',100,null);
		}
	}
}

?>