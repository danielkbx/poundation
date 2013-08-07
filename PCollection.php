<?php

namespace Poundation;

require_once 'PObject.php';

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
		return self::CollectionIndexNotFound;
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

	/**
	 * Returns the first object of the collection.
	 * @return object
	 */
	function firstObject() {
		return $this->objectForIndex(0);
	}
	
	/**
	 * Returns the last object of the collection.
	 * @return object
	 */
	function lastObject() {
		if ($this->count() > 0) {
			return $this->objectForIndex($this->count() - 1);
		} else {
			return NULL;
		}
	}
	
	/**
	 * Returns a string with all elements glued together with the given string.
	 * @param string $glue
	 * @return \Poundation\PString
	 */
	function string($glue) {
		return __(implode($glue, $this->map));
	}

	/**
	 * Filters the collection using the filter descriptor.
	 * @param PFilterDescriptor $descriptor
	 *
	 * @return bool
	 */
	function filterUsingFilterDescriptor(PFilterDescriptor $descriptor) {

		$indexesToRemove = array();

		foreach($this->map as $key => $value) {
			if (!$descriptor->doesElementMatch($value)) {
				$indexesToRemove[] = $key;
			}
		}

		foreach($indexesToRemove as $indexToRemove) {
			unset($this->map[$indexToRemove]);
		}

		return true;
	}

	/**
	 * Filters the collection by the given property name and value (using a filter descriptor internally).
	 * @param $property
	 * @param $value
	 *
	 * @return bool
	 */
	function filterByProperty($property, $value) {
		if (is_string($property)) {
			return $this->filterUsingFilterDescriptor(new PFilterDescriptor($property, $value));
		} else {
			return false;
		}
	}
	
	// Iteration methods
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current() {
		return current($this->map);
		//return $this->map [$this->iteratorPosition];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() {
		return key($this->map);
		//return $this->iteratorPosition;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() {
		return next($this->map);
		//++ $this->iteratorPosition;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		return reset($this->map);
		//$this->iteratorPosition = 0;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {
		return ($this->current() !== false);
		//return (isset ( $this->map [$this->iteratorPosition] ));
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
	
	/**
	 * Returns a PHP-native array.
	 * @return array
	 */
	public function nativeArray() {
		return $this->map;
	}
}

?>