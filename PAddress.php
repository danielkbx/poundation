<?php

namespace Poundation;

class PAddress extends \Poundation\PObject implements \JsonSerializable {

    const FIELDNAME_STREET = 'street';
    const FIELDNAME_ZIP = 'zip';
    const FIELDNAME_CITY = 'city';
    const FIELDNAME_COUNTRY = 'country';
    const FIELDNAME_COORDINATE = 'coordinate';

    private $street;
    private $zip;
    private $city;
    private $country;

    private $coordinate;

    /**
     * (PHP 5 >= 5.4.0)
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        $data = array();
        if ($this->getStreet()) {
            $data[self::FIELDNAME_STREET] = $this->getStreet();
        }
        if ($this->getZip()) {
            $data[self::FIELDNAME_ZIP] = $this->getZip();
        }
        if ($this->getCity()) {
            $data[self::FIELDNAME_CITY] = $this->getCity();
        }
        if ($this->getCountry()) {
            $data[self::FIELDNAME_COUNTRY] = $this->getCountry();
        }
        if ($this->getCoordinate()) {
            $data[self::FIELDNAME_COORDINATE] = $this->getCoordinate();
        }
        return $data;
    }

    /**
     * Creates the address from json.
     * @param $json
     * @return null|PAddress
     */
    static public function addressFromJSON($json) {
        $address = null;

        if (is_string($json)) {
            $json = json_decode($json);
        }

        if (is_array($json)) {
            $address = new PAddress();
            if (isset($json[self::FIELDNAME_STREET])) {
                $address->setStreet($json[self::FIELDNAME_STREET]);
            }
            if (isset($json[self::FIELDNAME_ZIP])) {
                $address->setZip($json[self::FIELDNAME_ZIP]);
            }
            if (isset($json[self::FIELDNAME_CITY])) {
                $address->setCity($json[self::FIELDNAME_CITY]);
            }
            if (isset($json[self::FIELDNAME_COUNTRY])) {
                $address->setCountry($json[self::FIELDNAME_COUNTRY]);
            }
            if (isset($json[self::FIELDNAME_COORDINATE])) {
                $coordinate = PCoordinate::addressFromJSON($json[self::FIELDNAME_COORDINATE]);
                if ($coordinate instanceof PCoordinate) {
                    $address->setCoordinate($coordinate);
                }
            }
        }

        return $address;
    }

    /**
     * Sets the street including the house number.
     * @param $street
     * @return PAddress
     */
    public function setStreet($street) {
        $this->street = $street;
        return $this;
    }

    /**
     * Returns the stree.
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * Sets the city.
     * @param $city
     * @return PAddress
     */
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    /**
     * Returns the city.
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Sets the country.
     * @param $country
     * @return PAddress
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * Returns the country.
     * @return string
     */
    public function getCountry() {
        return $this->country;
        return $this;
    }

    /**
     * Sets the zip code.
     * @param $zip
     * @return PAddress
     */
    public function setZip($zip) {
        $this->zip = $zip;
        return $this;
    }

    /**
     * Returns the zip code.
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * Returns the coordinate object.
     * @return null|PCoordinate
     */
    public function getCoordinate() {
        return $this->coordinate;
    }

    /**
     * Sets the coordinate.
     * @param \PCoordinate $coordinate
     * @return PAddress
     */
    public function setCoordinate(PCoordinate $coordinate) {
        $this->coordinate = $coordinate;
        return $this;
    }

    /**
     * Removes the coordinate.
     * @return PAddress
     */
    public function removeCoordinate() {
        $this->coordinate = null;
        return $this;
    }

	public function __toString() {

		$data = array();

		if ($this->street) {
			$data[] = $this->street;
		}

		$city = '';
		if ($this->zip) {
			$city = $this->zip;
		}

		if ($this->city) {
			$city.= ' ' . $this->city;
		}

		$data[] = trim($city);

		if ($this->country) {
			$data[] = $this->country;
		}


		return implode(',', $data);

	}

}
