<?php

namespace Poundation;

class PSortDescriptor extends PObject
{

	private $descriptor;
	private $sortDirection;

	private $publicProperty = null;
	private $publicAccessor = null;

	public function __construct($property, $sortDirection = SORT_ASC)
	{
		$this->descriptor    = $property;
		$this->sortDirection = $sortDirection;
	}

	public function getDescriptor()
	{
		return $this->descriptor;
	}

	public function getSortDirection()
	{
		return $this->sortDirection;
	}

	public function cmpObjectsByDescriptor($a, $b)
	{

		$property = $this->getDescriptor();

		$canCompare = false;

		$valueA = null;
		$valueB = null;

		if (is_null($this->publicProperty) && is_null($this->publicAccessor)) {

			$reflectionClass = new \ReflectionClass($a);

			if (property_exists($a, $this->descriptor) && property_exists($b, $this->descriptor)) {

				$propertyClass = $reflectionClass->getProperty($property);
				if ($propertyClass->isPublic()) {
					$this->publicProperty = $property;
				} else {
					$this->publicProperty = false;
				}
			}

			if (is_null($this->publicProperty) || $this->publicProperty === false) {

				$method = 'get' . PString::createFromString($property)->uppercaseAtBeginning();
				if (method_exists($a, $method)) {
					$methodClass = $reflectionClass->getMethod($method);
					if ($methodClass->isPublic()) {
						$this->publicAccessor = $method;
					} else {
						$this->publicAccessor = false;
					}
				}
			}

			if ((is_null($this->publicProperty) && is_null($this->publicAccessor)) || ($this->publicProperty === false && $this->publicAccessor === false)) {

				$method = $property;
				if (method_exists($a, $method)) {
					$methodClass = $reflectionClass->getMethod($method);
					if ($methodClass->isPublic()) {
						$this->publicAccessor = $method;
					} else {
						$this->publicAccessor = false;
					}
				}
			}



		}

		if (is_string($this->publicProperty)) {

			$canCompare = true;

			$valueA = $a->$property;
			$valueB = $b->$property;
		} else if (is_string($this->publicAccessor)) {
			$canCompare = true;

			$valueA = call_user_func(array(
										  $a,
										  $this->publicAccessor
									 ));
			$valueB = call_user_func(array(
										  $b,
										  $this->publicAccessor
									 ));
		}

		if ($canCompare) {

			if ($valueA instanceof \DateTime && $valueB instanceof \DateTime) {

				$valueA = $valueA->format('U');
				$valueB = $valueB->format('U');

			}

			$compareValue = strcasecmp((string)$valueA, (string)$valueB);
			if ($this->sortDirection == SORT_DESC) {
				$compareValue *= -1;
			}

			return $compareValue;

		} else {
			throw new \Exception('Descriptor does not match a property.');
		}

	}

}