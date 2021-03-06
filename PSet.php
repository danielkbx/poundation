<?php

namespace Poundation;

require_once 'PArray.php';
require_once 'PString.php';

use Poundation\PCollection;

/**
 * @abstract PSet manages a collection of objects. It contains every object only once.
 * @author danielkbx
 */
class PSet extends PArray {
	
	/*
	 * Adds an object to the set if it not part yet. 
	 * @param $object
	 * @return Poundation\PSet
	 */
	function add($object, $index = -1) {
		if ($this->contains ( $object ) === false) {
			parent::add($object, $index);
		}
		return $this;
	}

    /**
     * Removes the object from the set.
     * @param $object
     * @return PSet
     */
    function remove($object) {
        if ($object && $this->contains($object)) {
            $index = $this->indexOfObject($object);
            if ($index != PCollection::CollectionIndexNotFound) {
                unset($this->map[$index]);
            }
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
			throw new \Exception('Sets cannot handle a key.',100,null);
		}
	}
}

?>