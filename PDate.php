<?php

namespace Poundation;

class PDate extends PObject {

    private $data;

    public function __construct($value = null) {

        $dateValue = null;

        if (is_integer($value)) {
            $dateValue = new \DateTime();
            $dateValue->setTimestamp($value);
        } else if ($value instanceof \DateTime) {
            $dateValue = clone $value;
        } else if (is_string($value)) {
            $dateValue = new \DateTime($value);
        } else {
            $dateValue = new \DateTime('now');
        }

        if ($dateValue == null) {
            throw new \Exception('Cannot create dat object with this value "' + $value + '".');
        } else {
            $this->data = $dateValue;
        }
    }

    /**
     * Returns the native DateTime value.
     * @return \DateTime
     */
    public function getDateTime() {
        return $this->data;
    }

    /**
     * Returns a string formated in the native Doctrine way.
     * @return string|null
     */
    public function getInDoctrineFormat($includeTime = false) {
        $format = 'Y-m-d';
        if ($includeTime) {
            $format.= ' H:i:s.u';
        }
        return ($this->data) ? $this->getDateTime()->format($format) : null;
    }

    /**
     * Returns a ISO8601 formated string.
     * @return null|string
     */
    public function getInISO8601Format() {
        return ($this->data) ? $this->getDateTime()->format(\DateTime::ISO8601) : null;
    }

}
