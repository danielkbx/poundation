<?php
namespace Poundation;

class PURL {

	const SCHEME_HTTP = 'http';
	const SCHEME_HTTPS = 'https';
	
	/**
	 * @var PString
	 */
	private $URLString;
	
	/**
	 * @var PString
	 */
	private $scheme;
	/**
	 * @var PString
	 */
	private $host;
	/**
	 * @var Integer
	 */
	private $port;
	
	/**
	 * @var PString
	 */
	private $path;
	
	/**
	 *  Creates a new PURL object with the given string-based URL.
	 *  @return PURL
	 */
	function __construct($URLString) {
		if (!$URLString instanceof PString) {
			$URLString = new PString($URLString);
		}
		
		$this->URLString = $URLString;
		$components = $this->URLString->components(':');
		switch ($components->count()) {
			case 0:
				$this->scheme = NULL;
				$this->host = NULL;
				$this->port = 0;
				break;
			case 1:
				$this->scheme = self::SCHEME_HTTP;
				$this->host = $components->firstObject()->removeLeadingCharactersWhenMatching('//');
				$this->port = 0;
				break;
			case 2:
				$this->scheme = $components->firstObject();
				$host = $components->lastObject()->removeLeadingCharactersWhenMatching('//');
				$this->host = $host->substringToPositionOfString('/');
				$path = $host->substringFromPositionOfString('/');
				$this->path = ($path->length() > 0) ? $path : NULL;
				$this->port = 0;
				break;
			default:
				$this->scheme = $components->firstObject();
				$this->host = $components->objectForIndex(1)->removeLeadingCharactersWhenMatching('//');
				$portsAndPathComponents = $components->objectForIndex(2)->components('/');
				
				switch ($portsAndPathComponents->count()) {
					case 0:
						$this->port = 0;
						$this->path = NULL;
						break;
					case 1:
						$this->port = (int)$portsAndPathComponents->firstObject()->integerValue();
						$this->path = NULL;
						break;
					default:
						$this->port = $portsAndPathComponents->firstObject()->integerValue();
						unset($portsAndPathComponents[0]);
						$this->path = $portsAndPathComponents->string('/');
						break;
				}
				
				break;
		}
			
		
		if ($this->port == 0) {
			if ($this->scheme == self::SCHEME_HTTP) {
				$this->port = 80;
			} else if ($this->port == self::SCHEME_HTTPS) {
				$this->port = 443;
			}
		}
	}
	
	/**
	 * Returns the scheme of the request.
	 * @return \Poundation\PString
	 */
	public function scheme() {
		return $this->scheme;
	}
	
	/**
	 * Returns the host of the request.
	 * @return \Poundation\PString
	 */
	public function host() {
		return $this->host;
	}
	
	/**
	 * Returns the port of the request.
	 * @return integer
	 */
	public function port() {
		return $this->port;
	}
	
	/**
	 * Returns the path of the URL.
	 * @return \Poundation\PString
	 */
	public function path() {
		return $this->path;
	}
	
	/**
	 * Returns a list of components of the URL.
	 * @return \Poundation\PArray
	 */
	function domainComponents() {
		return 	$this->host->components('.');
	}
	
	/**
	 * Returns a string with the top-level domain.
	 * @return \Poundation\PString
	 */
	function domain() {
		$components = $this->domainComponents();
		$numberOfComponents = $components->count();
		switch ($numberOfComponents) {
			case 0:
				return NULL;
				break;
			case 1:
				return __($components->objectForIndex(0));
				break;
			case 2:
				return __($components->string('.'));
				break;
			default:
				return __($components[$numberOfComponents - 2])->appendString('.')->appendString($components[$numberOfComponents - 1]);
				break;
		}
	}
	
	/**
	 * Returns an array with all subdomains.
	 * @return \Poundation\PArray
	 */
	function subdomains() {
		$subdomains = new PArray();
		
		$components = $this->domainComponents();
		$numberOfComponents = $components->count();
		if ($numberOfComponents >= 3) {
			for ($i=0; $i < $numberOfComponents - 2; $i++) {
				$subdomains->add($components->objectForIndex($i));
			}			
		}
		return $subdomains;
	}
	
}

?>