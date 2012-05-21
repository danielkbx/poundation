<?php

namespace Poundation;

require_once ('PObject.php');

use Poundation\PObject;

/**
 * @abstract PCollection is an abstract class to manage collections.
 * @author danielkbx
 */
abstract class PCollection extends PObject implements \Iterator, \Countable, \ArrayAccess {
	
	const CollectionIndexNotFound = - 1;
	
	protected $map;
	protected $iteratorPosition;
	
	/**
	 */
	function __construct() {
		$this->map = array ();
		$this->iteratorPosition = 0;
	}
	
	/**
	 * Returns the number of elements.
	 * 
	 * @return integer
	 */
	function count() {
		return count ( $this->map );
	}
		
	/**
	 * Checks if the given element is contained in the collection.
	 * 
	 * @param $object The
	 *        	element to check
	 * @return boolean
	 */
	function contains($object) {
		return (in_array ( $object, $this->map ));
	}
	
	/**
	 * Returns the index of the given object.
	 * @param unknown_type $object
	 * @return integer
	 */
	function indexOfObject($object) {
		foreach ( $this->map as $key => $value ) {
			if ($value == $object) {
				return $key;
				break;
			}
		}
		return CollectionIndexNotFound;
	}
	
	/**
	 * Returns the object with the given index.
	 * @param integer $index
	 * @return Object
	 */
	function objectForIndex($index) {
		if (isset ( $this->map [$index] )) {
			return $this->map [$index];
		} else {
			return NULL;
		}
	}
	
	// Iteration methods
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->map [$this->iteratorPosition];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->iteratorPosition;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() {
		++ $this->iteratorPosition;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->iteratorPosition = 0;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {
		return (isset ( $this->map [$this->iteratorPosition] ));
	}
	
	// Array Access methods
	
	/*
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return (isset($this->map[$offset]));
	}
	
	/*
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		if ($this->offsetExists($offset)) {
			return $this->map[$offset];
		} else {
			return NULL;
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	abstract public function offsetSet($offset, $value);
		
	/*
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset($this->map[$offset]);
	}
}

?>