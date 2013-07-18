<?php

namespace Poundation;

class PCSS extends PObject
{

	private $selectors;

	public function __construct()
	{
		$this->selectors = new PArray();
	}

	/**
	 * Adds an selector element to the the CSS object.
	 * @param PCSS_Selector $selector
	 *
	 * @return $this
	 */
	public function addSelector(PCSS_Selector $selector)
	{
		$this->selectors->add($selector);
		return $this;
	}

	/**
	 * Creates a new selector with the given selector string, adds it and returns it.
	 * @param $selector
	 *
	 * @return PCSS_Selector
	 */
	public function addNewSelector($selector) {
		$newSelector = new PCSS_Selector($selector);
		$this->addSelector($newSelector);
		return $newSelector;
	}

	public function __toString()
	{
		return (string)$this->selectors->string("\n\n");
	}

}