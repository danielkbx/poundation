<?php

namespace Poundation;

class PCSS_Property extends PObject
{

	private $name;
	private $value;

	public function __construct($name, $value = null)
	{

		$this->name  = (string)$name;
		$this->value = (string)$value;
	}

	/**
	 * Returns the name of the property.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the value of the property.
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Sets the value of the property.
	 * @param $value
	 *
	 * @return $this
	 */
	public function setValue($value) {
		$this->value = (string)$value;
		return $this;
	}

	/**
	 * Adds a value to the property.
	 * @param $value
	 *
	 * @return $this
	 */
	public function addValue($value) {
		$this->value.= (string)$value;
		return $this;
	}

	public function __toString()
	{
		return $this->name . ': ' . $this->value . ';';
	}

}