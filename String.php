<?php

namespace Poundation;

require_once 'Object.php';

/**
 * Creates a new Poundation String object.
 * @param string $plainString
 * @return \Poundation\String
 */
function __($plainString='') {
    return new String($plainString);
}

/**
 * String manages a string object providing a set of string operations.
 * @abstract This class manages a string value. 
 * @author danielkbx
 */
class String extends Object {

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
	 * @return Poundation\String
	 */
	static function stringWithString($aString) {
		if ($aString instanceof String) $aString = $aString->__toString();
		return new String($aString);
	}

	/**
	 * Creates a new string from the given array by glueing all elements with the given glue string.
	 * @param array $array
	 * @param string $glue
	 * @return Poundation\String
	 */
	static function stringWithArray($array,$glue='') {
		$str = '';
		foreach($array as $component) {
			$str.= $component . $glue;
		}
		$str = self::stringWithString($str);
		return ($glue!='') ? $str->removeTrailingCharactersWhenMatching($glue) : $str;
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
	 * @return Poundation\String
	 */
	public function substring($startPosition, $length = 0) {
		if ($this->length() == 0) return String::stringWithString('');
		if ($length == 0) {
			return String::stringWithString(substr($this->_string, $startPosition));
		} else {
			return String::stringWithString(substr($this->_string, $startPosition, $length));
		}
	}

	/**
	 * Returns the first character(s) of the string.
	 * @param integer $length
	 * @return Poundation\String
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
	 * @return Poundation\String
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
	 * @return Poundation\String
	 */
	public function trim() {
		return String::stringWithString(trim($this->_string));
	}

	/**
	 * Returns a string with all characters being uppercase characters.
	 * @return Poundation\String
	 */
	public function uppercase() {
		return String::stringWithString(strtoupper($this->_string));
	}

	/**
	 * Returns a string where the charater at the given position is converted to uppercase.
	 * @param integer $position
	 * @return Poundation\String
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
	 * @return Poundation\String
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
	 * @return Poundation\String
	 */
	public function uppercaseAtBeginning($length=1) {
		if ($length == 0) {
			// nothing to capitalize
			return $this;
		} elseif ($length == 1) {
			// default behaviour of PHP so let's use it
			return String::stringWithString(ucfirst($this->_string));
		} else {
			// we uppercase the first characters and append the rest
			return $this->first($length)->toUppercase()->appendString($this->substring($length));
		}
	}

	/**
	 * Returns a string with the given number of characters in lower case.
	 * @param integer $length
	 * @return Poundation\String
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
	 * @return Poundation\String
	 */
	public function lowercase() {
		return String::stringWithString(strtolower($this->_string));
	}

	/**
	 * Returns a string where the needle has been replaced with the replacement String.
	 * @param String $needle
	 * @param String $replacement
	 * @return Poundation\String
	 */
	public function replace($needle,$replacement='') {
		return String::stringWithString(str_replace($needle,$replacement,$this->_string));
	}

	/**
	 * Removes the trailing characters if they match the given the string.
	 * @param String $char
	 * @return Poundation\String
	 */
	public function removeTrailingCharactersWhenMatching($char) {
	if ($char instanceof String) $char = $char->__toString();
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
	 * @return Poundation\String
	 */
	public function removeTrailingCharacters($length) {
		if ($length <= $this->length()) {
			return $this->substring(0,$this->length()-$length);
		} else return String::stringWithString('');
	}

	/**
	 * Removes the leading characters if they match the given the string.
	 * @param $char
	 * @return Poundation\String
	 */
	public function removeLeadingCharactersWhenMatching($char) {
		if ($char instanceof String) $char = $char->__toString();
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
	 * @return Poundation\String
	 */
	public function removeLeadingCharacters($length) {
		if ($length <= $this->length()) {
			return $this->substring($length);
		} else return String::stringWithString('');
	}

	/**
	 * Returns the string reversed.
	 * @return Poundation\String
	 */
	public function reverse() {
		return String::stringWithString(strrev($this->_string));
	}

	/**
	 * Returns the string appended with another string.
	 * @param String $appendix
	 * @return Poundation\String
	 */
	public function appendString($appendix) {
		return String::stringWithString($this->_string . $appendix);
	}

	/**
	 * Adds a string to the string.
	 * @param String $appendix
	 * @return Poundation\String
	 */
	public function addString($appendix) {
		$this->_string.= (string) $appendix;
		return $this;
	}

	/**
	 * Returns the string prepended by another string.
	 * @param String $prefix
	 * @return Poundation\String
	 */
	public function prependString($prefix) {
		return String::stringWithString($prefix . $this->_string);
	}

	/**
	 * Splits a string into an array.
	 * @param String $delimiter
	 * @return Poundation\String[]
	 */
	public function components($delimiter) {
		$tmpArray = explode($delimiter,$this->_string);
		$array = array();
		foreach($tmpArray as $component) {
			if ($component != '') {
				$array[] = String::stringWithString($component);
			}
		}
		return $array;
	}

	/**
	 * Returns true if the string contains the given string.
	 * @param String $String
	 * @return boolean
	 */
	public function contains($string) {
		return (strpos($this->_string,$string) !== false);
	}

	/**
	 * Returns the position of the first appearance of the given character.
	 * @param String $char
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
	 * @param String $char
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
	 * @param String $String
	 * @param boolean $caseSensitive
	 * @return Poundation\String
	 */
	public function substringFromPositionOfString($string,$caseSensitive=false) {
		if ($caseSensitive) {
			$str = strstr($this->_string,$string);
		} else {
			$str = stristr($this->_string,$string);
		}
		if ($str === false) $str = '';
		return String::stringWithString($str);
	}

	/**
	 * Returns a substring starting at the beginning to the first appearance of the given string.
	 * @param String $String
	 * @param boolean $caseSensitive
	 * @return Poundation\String
	 */
	public function substringToPositionOfString($string,$caseSensitive=false) {
		if ($caseSensitive) {
			$str = substr(strrev(strstr(strrev($this->_string), strrev($string))), 0, -strlen($string));
		} else {
			$str = substr(strrev(stristr(strrev($this->_string), strrev($string))), 0, -strlen($string));
		}
		if ($str === false) $str = '';
		return String::stringWithString($str);
	}

	/**
	 * Sets the first character to the given character if it is not already.
	 * @param String $char
	 * @return Poundation\String
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
	 * @param String $char
	 * @return Poundation\String
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
	 * @param String $seperator
	 * @return Poundation\String
	 */
	public function camelizeAtCharacter($seperator) {
		if ($this->contains($seperator)) {
			$parts = $this->components($seperator);
			$string = __('');
			$isFirstRun = YES;
			foreach($parts as $part) {
				$part = __($part);
				if (!$isFirstRun) {
					$part = $part->firstToUppercase();
				} else $isFirstRun = NO;
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
	 * @return Poundation\String
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
		return String::stringWithString(implode($fillingCharacters,$words));
	}

	/**
	 * Returns a string without whitespaces.
	 * @return Poundation\String
	 */
	public function stripWhitespace() {
		return $this->trim()->camelizeAtCharacter(' ');
	}

	/**
	 * Cuts the string after the given length.
	 * @param integer $length
	 * @return Poundation\String
	 */
	public function shorten($length) {
		if ($this->length() > $length) {
			return $this->substring(0,$length-3)->addString('...');
		} else return $this;
	}


	/**
	 * Cuts the string after given length and returns all of the string before the end character
	 * @param integer $length
	 * @param String $endChar
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
	 * @param String $endChar
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
	 * @return Poundation\String
	 */
	public function md5() {
		return String::stringWithString(md5($this->stringValue()));
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
	 * @return Poundation\String
	 */
	public function urlencode() {
		return String::stringWithString(urlencode($this->stringValue()));
	}

/**
	 * Returns the urldecoded String.
	 * @return Poundation\String
	 */
	public function urldecode() {
		return String::stringWithString(urldecode($this->stringValue()));
	}

	/**
	 * Strips all html tags except for the given ones.
	 * @param String $allowedTags
	 * @return Poundation\String
	 */
	public function stripTags($allowedTags='') {
		return __(strip_tags($this->stringValue(),$allowedTags));
	}
	
	/* (non-PHPdoc)
	 * @see \Poundation\Object::isEqual()
	 */
	 public function isEqual($otherObject) {
		return ($this->__toString() === $otherObject->__toString());	
	}

	
	
	
}

?>