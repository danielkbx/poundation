<?php

namespace Poundation;

class PCSS_Property extends PObject
{
    const DIRECTION_ALL    = null;
    const DIRECTION_LEFT   = 'left';
    const DIRECTION_TOP    = 'top';
    const DIRECTION_RIGHT  = 'right';
    const DIRECTION_BOTTOM = 'bottom';

    private $name;
    private $value;
    private $isImportant = false;

    public function __construct($name, $value = null, $important = false)
    {

        $this->name        = (string)$name;
        $this->value       = (string)$value;
        $this->isImportant = ($important == true);
    }

    /**
     * @param $name
     * @return PCSS_Property
     */
    public static function propertyWithName($name)
    {
        $property = new PCSS_Property($name);
        return $property;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public static function propertyWithNameAndValue ($name, $value) {
        return self::propertyWithName($name)->setValue($value);
    }

    /**
     * @param $name
     * @return $this
     */
    public static function importantPropertyWithName ($name) {
        return self::propertyWithName($name)->setImportant(true);
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public static function importantPropertyWithNameAndValue($name, $value) {
        return self::importantPropertyWithName($name)->setValue($value);
    }

    /**
     * Returns the name of the property.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of the property.
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value of the property.
     * @param $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = (string)$value;

        return $this;
    }

    /**
     * Adds a value to the property.
     * @param $value
     *
     * @return $this
     */
    public function addValue($value)
    {
        $this->value .= (string)$value;

        return $this;
    }

    /**
     * Returns true if the property is marked as important.
     * @return bool
     */
    public function isImportant()
    {
        return ($this->isImportant);
    }

    /**
     * Sets the important flag.
     * @param $flag
     * @return $this
     */
    public function setImportant($flag)
    {
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