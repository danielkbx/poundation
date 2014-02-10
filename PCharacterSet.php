<?php

namespace Poundation;

require_once 'PObject.php';
require_once 'PSet.php';
require_once 'PString.php';

use Poundation\PObject;

/**
 * @abstract A character set holds single characters used for splitting, comparing and trimming and is strongly related to strings.
 * @author danielkbx
 *        
 */
class PCharacterSet extends PObject {
	
	/**
	 * @var PSet
	 */
	private $_characters;
	
	function __construct() {
		$this->_characters = new PSet();
	}
	
	function __toString() {
		return PString::stringWithArray($this->_characters,'')->__toString();
	}
	
	/**
	 * Returns a new character set pre-setup with whitespace characters.
	 * @return \Poundation\PCharacterSet
	 */
	static function whitespaceCharacterSet() {
		$set = new PCharacterSet();
		$set->addCharacter(' ');
		$set->addCharacter("\n");
		$set->addCharacter("\t");
		$set->addCharacter("\r");
		$set->addCharacter("\0");
		$set->addCharacter("\x0B");
		return $set;
	}

    /**
     * Creates a new character set with all characters from the given string.
     * @param $string
     * @return PCharacterSet
     */
    static function characterSetWithCharactersFromString($string) {
        $set = new self();
        $set->addCharactersFromString($string);
        return $set;
    }
	
	/**
	 * Adds a single character to the character set.
	 * @param string $char
     * @return $this
	 * @throws \Exception
	 */
	function addCharacter($char) {
		$string = '';
		if (is_string($char)) {
			$string = $char;
		} else if (is_object($char)) {
			if ($char instanceof PString) {
				$string = $char->__toString();
			}
		} else {
			throw new \Exception('Only string values accepted.',108,null);
		}
		
		$length = strlen($string);
		if ($length == 0) {
			throw new \Exception('Cannot add empty string to character set.',109,null);
		} else if ($length > 1) {
			throw new \Exception('Can only add single characters to character set.',109,null);
		} else {
			$this->_characters->add($string);
		}
        return $this;
	}
	
	/**
	 * Adds all characters in a string to the character set.
	 * @param string $string
     * @return $this
	 * @throws \Exception
	 */
	function addCharactersFromString($string) {
		if (is_string($string)) {
			$characters = PString::createFromString($string);
		} else if (is_object($string)) {
			if ($string instanceof PString) {
				$characters = $string;
			}
		} else {
			throw new \Exception('Only string values accepted.',108,null);
		}
		
		$length = $characters->length();
		if ($length == 0) {
			throw new \Exception('Cannot add empty string to character set.',109,null);
		} else {
			$_this = $this;
			$characters->iterateByCharacter(function($char) use (&$_this) {
				$_this->addCharacter($char);
			});		
		}
        return $this;
	}

    /**
     * Returns the set of characters.
     * @return PSet
     */
    function set() {
        return PSet::create($this->_characters);
    }
}

?>