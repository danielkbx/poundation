<?php

namespace Poundation;

class PFilterDescriptor extends PObject
{

	private $descriptor;
	private $value;

	private $publicProperty = null;
	private $publicAccessor = null;

	public function __construct($property, $value)
	{
		$this->descriptor = $property;
		$this->value = $value;
	}

	public function getDescriptor()
	{
		return $this->descriptor;
	}

	public function doesElementMatch($element)
	{

		$property     = $this->getDescriptor();
		$canDetermine = false;
		$value        = null;

		if (is_null($this->publicProperty) && is_null($this->publicAccessor)) {

			$reflectionClass = new \ReflectionClass($element);

			if (property_exists($element, $this->descriptor)) {

				$propertyClass = $reflectionClass->getProperty($property);
				if ($propertyClass->isPublic()) {
					$this->publicProperty = $property;
				} else {
					$this->publicProperty = false;
				}
			}

			if (is_null($this->publicProperty) || $this->publicProperty === false) {

				$method = 'get' . PString::createFromString($property)->uppercaseAtBeginning();
				if (method_exists($element, $method)) {
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
				if (method_exists($element, $method)) {
					$methodClass = $reflectionClass->getMethod($method);
					if ($methodClass->isPublic()) {
						$this->publicAccessor = $method;
					} else {
						$this->publicAccessor = false;
					}
				}
			}

			if ((is_null($this->publicProperty) && is_null($this->publicAccessor)) || ($this->publicProperty === false && $this->publicAccessor === false)) {

				$method = 'is' . PString::createFromString($property)->uppercaseAtBeginning();;
				if (method_exists($element, $method)) {
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

			$canDetermine = true;

			$value = $element->$property;
		} else if (is_string($this->publicAccessor)) {
			$canDetermine = true;

			$value = call_user_func(array(
										 $element,
										 $this->publicAccessor
									));
		}

		if ($canDetermine) {
			return ($value === $this->value);
		} else {
			throw new \Exception('Cannot determine value for property ' . $property . '.');
		}
	}

}