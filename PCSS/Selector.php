<?php

namespace Poundation;

class PCSS_Selector extends PObject
{

	private $selector;

	private $properties;

	public function __construct($selector)
	{
		$this->selector   = (string)$selector;
		$this->properties = new PDictionary();

	}

	/**
	 * Adds a CSS property to the selector.
	 * @param PCSS_Property $property
	 *
	 * @return $this
	 */
	public function addProperty(PCSS_Property $property)
	{
		$this->properties->setValueForKey($property, $property->getName());
		return $this;
	}

	/**
	 * Creates a new property,  adds it and returns it.
	 * @param $name string
	 * @param $value string
	 *
	 * @return PCSS_Property
	 */
	public function addPropertyAndValue($name,$value) {
		$property = new PCSS_Property($name, $value);
		$this->addProperty($property);
		return $property;
	}

	/**
	 * Returns the property with the given name if existing.
	 * @param $name
	 *
	 * @return PCSS_Property|null
	 */
	public function getPropertyWithName($name) {
		return $this->properties->valueForKey($name);
	}

	/**
	 * Removes the given property.
	 * @param PCSS_Property $property
	 *
	 * @return $this
	 */
	public function removeProperty(PCSS_Property $property) {
		$this->properties->removeValue($property);
		return $this;
	}

	public function __toString()
	{

		$output = $this->selector . ' {' . "\n";

		if ($this->properties->count() > 0) {
			$output.= "\t";
		}
		$output .= (string)$this->properties->string("\n\t");

		$output .= "\n" . '}';

		return $output;

	}

}