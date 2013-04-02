<?php

namespace Poundation;

class PColor extends PObject implements \JsonSerializable
{

    private $red;
    private $green;
    private $blue;
    private $alpha;

    public function __construct($red, $green, $blue, $alpha = 1.0)
    {
        $this->red = max(0.0, min((float)$red, 1.0));
        $this->green = max(0.0, min((float)$green, 1.0));
        $this->blue = max(0.0, min((float)$blue, 1.0));
        $this->alpha = max(0.0, min((float)$alpha, 1.0));
    }

    /**
     * Creates a new color object from the given hex string. Both short and long versions are supported (#000, #000000), as are value with or without alpha values (#6661, #606060FF).
     * @param $string string
     * @return null|PColor
     */
    public static function colorFromString($string)
    {
        $color = null;

        if (!$string instanceof PString) {
            $string = __($string);
        }

        if ($string instanceof PString) {

            if ($string->hasPrefix('rgb')) {

                $values =  $string->substringBetween('(', ')');
                $components = $values->components(',');
                if ($components->count() == 4) {
                    $components[3] = __($components[3]->floatValue() * 255.0);
                } else {
                    $components[3] = __('255');
                }

                if ($components instanceof PArray && $components->count() == 4) {
                    $red = $components[0]->floatValue() / 255;
                    $green = $components[1]->floatValue() / 255;
                    $blue = $components[2]->floatValue() / 255;
                    $alpha = $components[3]->floatValue() / 255;
                    $color = new PColor($red, $green, $blue, $alpha);
                }

            } else {

                $components = null;
                $hexValue = $string->removeLeadingCharactersWhenMatching('#');
                switch ($hexValue->length()) {
                    case 0:
                        // The dark side of the moon.
                        $components = PArray::create(array(0.0, 0.0, 0.0, 1.0));
                        break;
                    case 3:
                        // the short non-alpha version.
                        $components = $hexValue->toArray(1);
                        foreach($components as $key=>$component) {
                            $components[$key] = $component . $component;
                        }
                        $components->add('FF');
                        break;
                    case 4:
                        // the short with-alpha version
                        $components = $hexValue->toArray(1);
                        foreach($components as $key=>$component) {
                            $components[$key] = $component . $component;
                        }
                        break;
                    case 6:
                        // the default long non-alpha version
                        $components = $hexValue->toArray(2)->add('FF');
                        break;
                    case 8:
                        // the long with-alpha version
                        $components = $hexValue->toArray(2);
                        break;
                }
                if ($components instanceof PArray && $components->count() == 4) {
                    $red = hexdec($components[0]) / 255;
                    $green = hexdec($components[1]) / 255;
                    $blue = hexdec($components[2]) / 255;
                    $alpha = hexdec($components[3]) / 255;
                    $color = new PColor($red, $green, $blue, $alpha);
                }
            }
        }

        return $color;

    }

    /**
     * Sets the alpha infomation of the color. An alpha value of 1.0 means full visibility while 0.0 means full transparency.
     * @param $alpha float
     * @return PColor
     */
    public function setAlpha($alpha)
    {
        $this->alpha = $alpha;
        return $this;
    }

    /**
     * Returns the alpha information of the color. An alpha value of 1.0 means full visibility while 0.0 means full transparency.
     * @return float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * Sets the blue fraction of the color.
     * @param $blue float
     * @return PColor
     */
    public function setBlue($blue)
    {
        $this->blue = $blue;
        return $this;
    }

    /**
     * Returns the blue fraction of the color.
     * @return float
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Sets the green fraction of the color.
     * @param $green float
     * @return PColor
     */
    public function setGreen($green)
    {
        $this->green = $green;
        return $this;
    }

    /**
     * Returns the green fraction of the color.
     * @return float
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * Sets the red fraction of the color.
     * @param $red float
     * @return PColor
     */
    public function setRed($red)
    {
        $this->red = $red;
        return $this;
    }

    /**
     * Returns the red fraction of the color.
     * @return float
     */
    public function getRed()
    {
        return $this->red;
    }

    public function __toString() {
        return $this->rgbaString();
    }

    /**
     * (PHP 5 >= 5.4.0)
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        return $this->getHexString(true,false);
    }


    /**
     * Returns a CSS rgb expression e.g. rgb(120,17,25).
     * @return string
     */
    public function rgbString() {
        $values = array(
            $this->red * 255,
            $this->green * 255,
            $this->blue * 255
        );
        return 'rgb(' . implode(',',$values) . ')';
    }

    /**
     * Returns a CSS rgba expression including the alpha value e.g. rgba(120,17,25,0.5).
     * @return string
     */
    public function rgbaString() {
        $values = array(
            $this->red * 255,
            $this->green * 255,
            $this->blue * 255,
            $this->alpha
        );
        return 'rgba(' . implode(',',$values) . ')';
    }

    /**
     * Returns a hex expression.
     * @param bool $includeAlpha
     * @param bool $preferShortVersion
     * @return string
     */
    public function getHexString($includeAlpha = false, $preferShortVersion = true) {
        $components = array(
            __(dechex($this->getRed() * 255)),
            __(dechex($this->getGreen() * 255)),
            __(dechex($this->getBlue() * 255))
        );

        if ($includeAlpha) {
            $components[] = __(dechex($this->getAlpha() * 255));
        }

        $hex = '#';
        $shortHex = '#';
        $useShortHex = true;
        foreach ($components as $component) {
            if ($component instanceof PString) {
                if ($component->length() == 1) {
                    $component = $component->prependString('0');
                }

                if ($component->length() == 2) {
                    $hex.= (string)$component;
                    if ($component->first() == $component->last()) {
                        $shortHex.= (string)$component->first();
                    } else {
                        $useShortHex = false;
                    }
                }
            }
        }

        return ($useShortHex) ? $shortHex : $hex;
    }
}
