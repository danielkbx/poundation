<?php

namespace Poundation;

require_once 'PObject.php';
require_once 'PArray.php';
require_once 'PCharacterSet.php';

/**
 * @abstract String manages a string object providing a set of string operations. 
 * @author danielkbx
 */
class PString extends PObject {

	private $_string='';

	function __construct($plainString='') {
		$this->_string = $plainString;
	}
	
	public function __toString() {
		return $this->_string;
	}

	/**
	 * Creates a new string object with the given String.
	 * @param $aString
	 * @return Poundation\PString
	 */
	static function createFromString($aString) {
		if ($aString instanceof PString) $aString = $aString->__toString();
		return new PString($aString);
	}

	/**
	 * Creates a new string from the given array by glueing all elements with the given glue string.
	 * @param array $array
	 * @param string $glue
	 * @return Poundation\PString
	 */
	static function stringWithArray($array,$glue='') {
		if ($array instanceof PCollection) {
			return $array->string($glue);
		} else {
			return implode($glue, $array);
		}
	}

	/**
	 * Returns the length of the string.
	 * @return integer
	 */
	public function length() {
		return strlen($this->_string);
	}

	/**
	 * Returns true if the string does not contain any characters.
	 * @return boolean
	 */
	public function isEmpty() {
		return ($this->length() == 0);
	}


	/**
	 * Returns a substring beginning at $startPosition with the given $length or to the end, if no length is given.
	 * @param integer $startPosition
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function substring($startPosition, $length = 0) {
		if ($this->length() == 0) return PString::createFromString('');
		if ($length == 0) {
			return PString::createFromString(substr($this->_string, $startPosition));
		} else {
			return PString::createFromString(substr($this->_string, $startPosition, $length));
		}
	}

	/**
	 * Returns the first character(s) of the string.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function first($length=1) {
		if ($length == 0) {
			return __();
		} else {
			return $this->substring(0,$length);
		}
	}

	/**
	 * Returns the last character(s) of the string.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function last($length=1) {
		if ($length == 0) {
			return __();
		} else {
			return substr($this->_string,$this->length()-$length);
		}
	}

	/**
	 * Returns a string without leading and trailing whitespace.
	 * @return Poundation\PString
	 */
	public function trim() {
		return PString::createFromString(trim($this->_string));
	}

	/**
	 * Returns a string with all characters being uppercase characters.
	 * @return Poundation\PString
	 */
	public function uppercase() {
		return PString::createFromString(strtoupper($this->_string));
	}

	/**
	 * Returns a string where the charater at the given position is converted to uppercase.
	 * @param integer $position
	 * @return Poundation\PString
	 */
	public function uppercaseAtPosition($position) {
		$start = $this->first($position);
		$char = $this->substring($position,1)->toUppercase();
		$end = $this->substring($position + 1);
		return $start->addString($char)->addString($end);
	}

	/**
	 * Returns a string where the charater at the given position is converted to lowercase.
	 * @param integer $position
	 * @return Poundation\PString
	 */
	public function lowercaseAtPosition($position) {
		$start = $this->first($position);
		$char = $this->substring($position,1)->toLowercase();
		$end = $this->substring($position + 1);
		return $start->addString($char)->addString($end);
	}

	/**
	 * Returns the string with the first given number of characters as capitals.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function uppercaseAtBeginning($length=1) {
		if ($length == 0) {
			// nothing to capitalize
			return $this;
		} elseif ($length == 1) {
			// default behaviour of PHP so let's use it
			return PString::createFromString(ucfirst($this->_string));
		} else {
			// we uppercase the first characters and append the rest
			return $this->first($length)->toUppercase()->appendString($this->substring($length));
		}
	}

	/**
	 * Returns a string with the given number of characters in lower case.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function lowercaseAtBeginning($length=1) {
		if ($length == 0) {
			return $this;
		} else {
			// we lowercase the first characters and append the rest.
			return $this->first($length)->toLowercase()->appendString($this->substring($length));
		}
	}

	/**
	 * Returns the string with all characters being lowercase characters.
	 * @return Poundation\PString
	 */
	public function lowercase() {
		return PString::createFromString(strtolower($this->_string));
	}

	/**
	 * Returns a string where the needle has been replaced with the replacement String.
	 * @param PString $needle
	 * @param PString $replacement
	 * @return Poundation\PString
	 */
	public function replace($needle,$replacement='') {
		return PString::createFromString(str_replace($needle,$replacement,$this->_string));
	}

	/**
	 * Removes the trailing characters if they match the given the string.
	 * @param PString $char
	 * @return Poundation\PString
	 */
	public function removeTrailingCharactersWhenMatching($char) {
	if ($char instanceof PString) $char = $char->__toString();
		if (strlen($char)==0) {
			return $this;
		} else {
			if ($char == $this->last(strlen($char))) {
				return $this->substring(0,$this->length() - strlen($char));
			} else {
				return $this;
			}
		}
	}

	/**
	 * Removes the given number of trailing characters.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function removeTrailingCharacters($length) {
		if ($length <= $this->length()) {
			return $this->substring(0,$this->length()-$length);
		} else return PString::createFromString('');
	}

	/**
	 * Removes the leading characters if they match the given the string.
	 * @param $char
	 * @return Poundation\PString
	 */
	public function removeLeadingCharactersWhenMatching($char) {
		if ($char instanceof PString) $char = $char->__toString();
		if (strlen($char)==0) {
			return $this;
		} else {
			if ($char == $this->first(strlen($char))) {
				return $this->substring(strlen($char));
			} else {
				return $this;
			}
		}
	}

	/**
	 * Removes the given number of leading characters.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function removeLeadingCharacters($length) {
		if ($length <= $this->length()) {
			return $this->substring($length);
		} else return PString::createFromString('');
	}

	/**
	 * Returns the string reversed.
	 * @return Poundation\PString
	 */
	public function reverse() {
		return PString::createFromString(strrev($this->_string));
	}

	/**
	 * Returns the string appended with another string.
	 * @param PString $appendix
	 * @return Poundation\PString
	 */
	public function appendString($appendix) {
		return PString::createFromString($this->_string . $appendix);
	}

	/**
	 * Adds a string to the string.
	 * @param PString $appendix
	 * @return Poundation\PString
	 */
	public function addString($appendix) {
		$this->_string.= (string) $appendix;
		return $this;
	}

	/**
	 * Returns the string prepended by another string.
	 * @param PString $prefix
	 * @return Poundation\PString
	 */
	public function prependString($prefix) {
		return PString::createFromString($prefix . $this->_string);
	}

	/**
	 * Splits a string into an array. The returns PArray contains a PString for every compontent.
	 * @param PString $delimiter
	 * @return Poundation\PArray
	 */
	public function components($delimiter) {
		$tmpArray = explode($delimiter,$this->_string);
		$array = new PArray();
		foreach($tmpArray as $component) {
			if ($component != '') {
				$array[] = PString::createFromString($component);
			}
		}
		return $array;
	}

	/**
	 * Returns true if the string contains the given string.
	 * @param PString $String
	 * @return boolean
	 */
	public function contains($string) {
		return (strpos($this->_string,$string) !== false);
	}

	/**
	 * Returns the position of the first appearance of the given character.
	 * @param PString $char
	 * @param boolean $caseSensitive
	 * @param integer $offset
	 * @return integer
	 */
	public function firstAppearanceOfString($char,$caseSensitive=false,$offset=0) {
		if ($caseSensitive) {
			return strpos($this->_string,$char,$offset);
		} else {
			return stripos($this->_string,$char,$offset);
		}
	}

	/**
	 * Returns the position of the last appearance of the gievn character.
	 * @param PString $char
	 * @param boolean $caseSensitive
	 * @param integer $offset
	 * @return integer
	 */
	public function lastAppearanceOfString($char,$caseSensitive=false,$offset=0) {
	if ($caseSensitive) {
			return strrpos($this->_string,$char,$offset);
		} else {
			return strripos($this->_string,$char,$offset);
		}
	}

	/**
	 * Returns a substring starting at the position of the first appearance of the given string to the end.
	 * @param PString $String
	 * @param boolean $caseSensitive
	 * @return Poundation\PString
	 */
	public function substringFromPositionOfString($string,$caseSensitive=false) {
		if ($caseSensitive) {
			$pos=strpos($this->_string,$string);
		} else {
			$pos=strpos(strtolower($this->_string),strtolower($string));
		}
		
		if ($pos === false) {
			return __($this->_string);
		} else {
			return __(substr($this->_string,$pos));
		}
	}

	/**
	 * Returns a substring starting at the beginning to the first appearance of the given string.
	 * @param PString $String
	 * @param boolean $caseSensitive
	 * @return Poundation\PString
	 */
	public function substringToPositionOfString($string,$caseSensitive=false) {
		if ($caseSensitive) {
			$pos=strpos($this->_string,$string);
		} else {
			$pos=strpos(strtolower($this->_string),strtolower($string));
		}
		
		if ($pos === false) {
			return __($this->_string);
		} else {
			return __(substr($this->_string,0,$pos));
		}
	}

	/**
	 * Sets the first character to the given character if it is not already.
	 * @param PString $char
	 * @return Poundation\PString
	 */
	public function ensureFirstCharacter($char) {
		if (substr($this->_string,0,strlen($char))!=$char) {
			return $this->prependString($char);
		} else {
			return $this;
		}
	}

	/**
	 * Sets the last character to the given character if it is not already.
	 * @param PString $char
	 * @return Poundation\PString
	 */
	public function ensureLastCharacter($char) {
		if (substr($this->_string,$this->length()-strlen($char))!=$char) {
			return $this->appendString($char);
		} else {
			return $this;
		}
	}

	/**
	 * Returns the String which has been camelized at the occurances of the given character.
	 * @param PString $seperator
	 * @return Poundation\PString
	 */
	public function camelizeAtCharacter($seperator) {
		if ($this->contains($seperator)) {
			$parts = $this->components($seperator);
			$string = __('');
			$isFirstRun = true;
			foreach($parts as $part) {
				$part = __($part);
				if (!$isFirstRun) {
					$part = $part->firstToUppercase();
				} else $isFirstRun = false;
				$string = $string->appendString($part);
			}
			return $string->firstToLowercase();
		}
		return $this;
	}

	/**
	 * Creates a string where camelized sequeenzes have been converted to contain whitespaces.
	 * This is the opposite operation to camelizing a string.
	 * @param string $fillingCharacters
	 * @return Poundation\PString
	 */
	public function splitIntoWordAtCapitals($fillingCharacters = ' ') {
		$length = $this->length();
		$positions = array();
		for($i = 0; $i < $length; $i++) {
			$char = $this->substring($i,1);
			if ((string) $char != (string) $char->toLowercase()) {
				$positions[] = $i;
			}
		}
		$words = array();
		$start = 0;
		$positions[] = $length;
		foreach($positions as $position) {
			$words[] = $this->substring($start,$position - $start)->toLowercase();
			$start = $position;
		}
		return PString::createFromString(implode($fillingCharacters,$words));
	}

	/**
	 * Returns a string without whitespaces.
	 * @return Poundation\PString
	 */
	public function stripWhitespace() {
		return $this->trim()->camelizeAtCharacter(' ');
	}

	/**
	 * Cuts the string after the given length.
	 * @param integer $length
	 * @return Poundation\PString
	 */
	public function shorten($length) {
		if ($this->length() > $length) {
			return $this->substring(0,$length-3)->addString('...');
		} else return $this;
	}


	/**
	 * Cuts the string after given length and returns all of the string before the end character
	 * @param integer $length
	 * @param PString $endChar
	 */
	public function shortenAfterSentence($length, $endChar='.'){
		if ($this->length() > $length) {

			$subStr = $this->substring(0,$length);
			return __($subStr)->substring(0,strrpos($subStr,$endChar)+1);;
		} else return $this;
	}
	
	/**
	 * Cuts the String after given length and returns all of the String befor end character
	 * @param integer $length
	 * @param PString $endChar
	 */
	public function shortenAfterChar($length, $char='.'){
		if ($this->length() > $length) {

			$subStr = $this->substring(0,$length);
			return __($subStr)->substring(0,strrpos($subStr,$char)+1);;
		} else return $this;
	}


	public function stringValue() {
		return $this->__toString();
	}

	/**
	 * Returns the md5 of the String.
	 * @return Poundation\PString
	 */
	public function md5() {
		return PString::createFromString(md5($this->stringValue()));
	}

	public function isUppercaseAtPosition($position) {
		$position = $this->substring($position,1);
		if (is_numeric((string) $position) || $position->length() == 0) {
			return false;
		} else {
			$uppercase = $position->toUppercase();
			return ($position->stringValue() == $uppercase->stringValue());
		}

	}

	/**
	 * Returns the urlencoded String.
	 * @return Poundation\PString
	 */
	public function urlencode() {
		return PString::createFromString(urlencode($this->stringValue()));
	}

/**
	 * Returns the urldecoded String.
	 * @return Poundation\PString
	 */
	public function urldecode() {
		return PString::createFromString(urldecode($this->stringValue()));
	}
	
	/**
	 * Iterates over every character of the string and executes the given function. The paramters passed in are the character and a boolean indication if the call is the last one.
	 * @param Closure $function($character,$isLast) 
	 */
	public function iterateByCharacter($function) {
		$length = $this->length();
		if ($length > 0) {
			for ($i = 0; $i < $length; $i++) {
				$character = $this->substring($i,1);
				$isLast = ($i == ($length - 1));
				$function($character, $isLast);
			}
		}
	}

	/**
	 * Strips all html tags except for the given ones.
	 * @param PString $allowedTags
	 * @return Poundation\PString
	 */
	public function stripTags($allowedTags='') {
		return __(strip_tags($this->stringValue(),$allowedTags));
	}
	
	/**
	 * Returns the integer value of the string.
	 * @return integer
	 */
	public function integerValue() {
		return (int)$this->_string;
	}
	
	/**
	 * Returns the float value of the string.
	 * @return float
	 */
	public function floatValue() {
		return (float)$this->_string;
	}
	
	/**
	 * Returns the boolean value of the string.
	 * @return boolean
	 */
	public function boolValue() {
		return (boolean)$this->_string;
	}


	
	/* (non-PHPdoc)
	 * @see \Poundation\Object::isEqual()
	 */
	 public function isEqual($otherObject) {
		return ($this->__toString() === $otherObject->__toString());	
	}

	
	
	
}

?>