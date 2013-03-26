<?php

namespace Poundation;

class PCoordinate extends PObject implements \JsonSerializable  {

    const LATITUDE_MINIMUM = -90;
    const LATITUDE_MAXIMUM = 90;
    const LONGITUDE_MINIMUM = -180;
    const LONGITUDE_MAXIMUM = 180;

    private $lat;
    private $lon;

    /**
     * Creates a new coordinate if the given values are valid.
     * @param $latitude
     * @param $longitude
     * @return null|PCoordinate
     */
    static public function createCoordinate($latitude, $longitude) {
        $coordinate = null;

        if (is_float($latitude) && is_float($longitude)) {
            if (self::LATITUDE_MINIMUM <= $latitude && $latitude <= self::LATITUDE_MAXIMUM &&
                self::LONGITUDE_MINIMUM <= $longitude && $longitude <= self::LONGITUDE_MAXIMUM) {
                $coordinate = new PCoordinate($latitude, $longitude);
            }
        }

        return $coordinate;
    }

    public function __construct($latitude, $longitude) {

        $this->lat = (float)$latitude;
        $this->lon = (float)$longitude;

    }

    /**
     * (PHP 5 >= 5.4.0)
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        return array(
            'lat'   => $this->getLatitude(),
            'lon'   => $this->getLongitude()
        );
    }


    /**
     * Sets the coordinate's latitude.
     * @param float $lat
     * @return PAddress
     */
    public function setLatitude($lat) {
        if (self::LATITUDE_MINIMUM < $lat && $lat <= self::LATITUDE_MAXIMUM) {
            $this->lat = $lat;
        }
        return $this;
    }

    /**
     * Returns the coordinate's latitude.
     * @return float
     */
    public function getLatitude() {
        return $this->lat;
    }

    /**
     * Sets the coordinate's latitude.
     * @param $lon
     * @return PAddress
     */
    public function setLongitude($lon) {
        if (self::LONGITUDE_MINIMUM <= $lon && $lon <= self::LONGITUDE_MAXIMUM) {
            $this->lon = $lon;
        }
        return $this;
    }

    /**
     * Returns the coordinate's longitude.
     * @return float
     */
    public function getLongitude() {
        return $this->lon;
    }
}