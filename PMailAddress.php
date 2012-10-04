<?php

namespace Poundation;

include_once 'PString.php';

class PMailAddress {

	private $user;
	private $host;
	
	function __construct($email = '') {
		$components = __($email)->components('@');
		switch ($components->count()) {
			case 2:
				$this->setUser($components[0]);
				$this->setHost($components[1]);				
				break;
		}
	}
	
	static function createFromString($mail) {
		if (self::verifyAddress($mail)) {
			$mail = new PMailAddress($mail);
			return $mail;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Sets the user of the mail address (the part to the @).
	 * @param string $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}
	
	/**
	 * Returns the user of the mail address.
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * Sets the host of the mail address (the part starting at the @).
	 * @param $host
	 */
	public function setHost($host) {
		$this->host = $host;
	}
	
	/**
	 * Returns the host of the address.
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
	
	/**
	 * Verifies the mail address and returns true if it is valid.
	 * @return boolean
	 */
	public function verify() {
		return self::verifyAddress($this->mailAddressFromComponents($this->getUser(), $this->getHost()));
	}
	
	/**
	 * Verifies a mail address and returns true if it is valid.
	 * @param string $mailAddress
	 * @return boolean
	 */
	static function verifyAddress($mailAddress) {
		return (filter_var($mailAddress, FILTER_VALIDATE_EMAIL));
	}
	
	private function mailAddressFromComponents($user,$host) {
		return $user . '@' . $host;
	}
	
	public function __toString() {
		return $this->mailAddressFromComponents($this->getUser(), $this->getHost());
	}
}

?>