<?php

namespace Poundation;

class PCSS_Property extends PObject
{
    const DIRECTION_ALL = null;
    const DIRECTION_LEFT = 'left';
    const DIRECTION_TOP = 'top';
    const DIRECTION_RIGHT = 'right';
    const DIRECTION_BOTTOM = 'bottom';

    private $name;
	private $value;
    private $isImportant = false;

	public function __construct($name, $value = null, $important = false)
	{

		$this->name  = (string)$name;
		$this->value = (string)$value;
        $this->isImportant = ($important == true);
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

    /**
     * Returns true if the property is marked as important.
     * @return bool
     */
    public function isImportant() {
        return ($this->isImportant);
    }

    /**
     * Sets the important flag.
     * @param $flag
     * @return $this
     */
    public function setImportant($flag) {
        $this->isImportant = ($flag == true);
        return $this;
    }

	public function __toString()
	{
        $value = $this->name . ': ' . $this->value;

        if ($this->isImportant()) {
            $value .= ' !important';
        }

		return $value . ';';
	}

}