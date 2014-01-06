<?php

namespace Poundation;

class PCSS_Selector extends PObject
{

    private $selector;

    private $properties;

    public function __construct($selector)
    {
        $this->selector = (string)$selector;
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
    public function addPropertyAndValue($name, $value, $important = false)
    {
        $property = new PCSS_Property($name, $value, $important);
        $this->addProperty($property);
        return $property;
    }

    /**
     * Returns the property with the given name if existing.
     * @param $name
     *
     * @return PCSS_Property|null
     */
    public function getPropertyWithName($name)
    {
        return $this->properties->valueForKey($name);
    }

    /**
     * Removes the given property.
     * @param PCSS_Property $property
     *
     * @return $this
     */
    public function removeProperty(PCSS_Property $property)
    {
        $this->properties->removeValue($property);
        return $this;
    }

    /**
     * Sets the value of the named property. If the property does not exist yet, it is created.
     * @param $value
     * @param $name
     * @return $this
     */
    public function setValueForPropertyWithName($value, $name, $important = false)
    {
        if (!is_null($value)) {

            $property = $this->getPropertyWithName($name);
            if (!$property instanceof PCSS_Property) {
                $property = new PCSS_Property($name);
                $this->addProperty($property);
            }

            $property->setValue($value);
            if ($important != $property->isImportant()) {
                $property->setImportant($important);
            }
        } else {

            $property = $this->getPropertyWithName($name);
            if (!is_null($property)) {
                $this->removeProperty($property);
            }
        }
        return $this;
    }

    /**
     * Sets the background color.
     * @param $color
     * @return $this
     */
    public function setBackgroundColor($color, $important = false)
    {
        if (is_string($color) || is_null($color) || $color instanceof PColor) {

            $value = null;
            if (is_string($color)) {
                $value = $color;
            } else {
                if ($color instanceof PColor) {
                    $value = $color->getHexString(false);
                }
            }

            $this->setValueForPropertyWithName($value, 'background-color', $important);
        }

        return $this;
    }

    /**
     * Sets the border color.
     * @param $color
     * @return $this
     */
    public function setBorderColor($color, $important = false, $direction = PCSS_Property::DIRECTION_ALL) {
        if (is_string($color) || is_null($color) || $color instanceof PColor) {

            $value = null;
            if (is_string($color)) {
                $value = $color;
            } else {
                if ($color instanceof PColor) {
                    $value = $color->getHexString(false);
                }
            }

            $name = 'border-';
            if (!is_null($direction)) {
                $name .= $direction . '-';
            }
            $name .= 'color';

            $this->setValueForPropertyWithName($value, $name, $important);
        }

        return $this;
    }

    public function __toString()
    {

        $output = $this->selector . ' {' . "\n";

        if ($this->properties->count() > 0) {
            $output .= "\t";
        }
        $output .= (string)$this->properties->string("\n\t");

        $output .= "\n" . '}';

        return $output;

    }

}