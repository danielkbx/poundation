<?php

namespace Poundation;

class PTemplate {

	private $template;
	private $rendered = null;

	private $fields = array();


	public function __construct($content = null) {
		$this->setTemplate($content);
	}

	/**
	 * Sets the raw content of the template.
	 *
	 * @param $content
	 *
	 * @return $this
	 */
	public function setTemplate($content) {

		$this->template = (string)$content;
		$this->rendered = null;

		return $this;
	}

	/**
	 * Sets the value of a field.
	 *
	 * @param $fieldName string
	 * @param $value     string
	 *
	 * @return $this
	 */
	public function setField($fieldName, $value) {
		$fieldName = __($fieldName);
		if ($fieldName->length() > 2) {

			$name = $fieldName->removeLeadingCharactersWhenMatching('%')->removeTrailingCharactersWhenMatching('%')->uppercase();

			$this->fields[(string)$name] = (string)$value;
			$this->rendered              = null;
		}

		return $this;
	}

	/**
	 * Sets multiple fields at once.
	 *
	 * @param $array
	 *
	 * @return $this
	 */
	public function setFields($array) {

		if (is_array($array)) {
			foreach ($array as $name => $value) {
				$this->setField($name, $value);
			}
		}

		return $this;

	}

	/**
	 * Returns the value of a field.
	 *
	 * @param $fieldName
	 *
	 * @return null|string
	 */
	public function getField($fieldName) {
		$fieldName = strtoupper($fieldName);
		return (isset($this->fields[$fieldName]) ? $this->fields[$fieldName] : null);
	}

	/**
	 * Returns the rendered string
	 *
	 * @return string
	 */
	public function renderedString() {
		if (is_null($this->rendered)) {

			if (is_string($this->template)) {
				$rendered = __($this->template);

				foreach ($this->fields as $name => $value) {
					$rendered = $rendered->replace('%' . $name . '%', $value);
				}

				$this->rendered = (string)$rendered;
			}

		}

		return $this->rendered;
	}

	public function __toString() {
		return $this->renderedString();
	}

}