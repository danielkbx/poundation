<?php
namespace Poundation\Server;

use Poundation\PString;
use Poundation\PURL;

class PRequest {
	
	private static $instance;
	
	private $requestedURL;
	
	static function request() {
		
		if (!isset(self::$instance)) {
			self::$instance = new PRequest();
			self::$instance->_init();
		}
		return self::$instance;
	}
	
	private function _init() {
		
		if(!isset($_SERVER['REQUEST_URI'])){
			$URI = $_SERVER['PHP_SELF'];
		}else{
			$URI =    $_SERVER['REQUEST_URI'];
		}

		$fullURL = PString::stringWithString('');
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$fullURL->addString('https://');
			$expectedPort = 443;
		} else {
			$fullURL->addString('http://');
			$expectedPort = 80;
		}
		
		$fullURL->addString($_SERVER['SERVER_NAME']);
		
		$serverPort = (int)$_SERVER['SERVER_PORT']; 
		if ($serverPort != $expectedPort) {
			$fullURL->addString(':' . $serverPort);
		}
	
		$fullURL->addString($URI);
				
		$this->requestedURL = new PURL($fullURL);
	}
	
	/**
	 * Returns the URL of the request.
	 * @return \Poundation\PURL
	 */
	public function getURL() {
		return $this->requestedURL;
	}
	
}

?>