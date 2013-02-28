<?php

namespace Poundation;

class PError extends PObject {

    protected $domain;
    protected $description;

    function __construct($domain,$description) {
        $this->domain = $domain;
        $this->description = $description;
    }


    /**
     * Creates a new generic error.
     * @param $domain
     * @param $description
     * @return PError
     */
    public static function createGenericError($domain,$description) {
        $error = new PError($domain,$description);
        return $error;
    }

    /**
     * Returns the error domain.
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * Returns the error description.
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function __toString() {
        return 'Error ' . $this->getDomain() . ': ' . $this->getDescription();
    }

}