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
	 * 
	 * @var array
	 */
	private $parameters = array();

    /**
     * Returns a new URL object if it can be constructed.
     * @param $urlString
     * @return null|PURL
     */
    public static function URLWithString($urlString) {
        $url = new self($urlString);
        if ($url->host()->length() == 0) {
            return null;
        } else {
            return $url;
        }
    }

    /**
     * Checks if a given string has the structure of an URL.
     * @param $urlString
     * @return bool
     */
    public static function isValidURLString($urlString) {
        $url = self::URLWithString($urlString);
        return ($url instanceof PURL);
    }

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
				$this->scheme = __('');
				$this->host = __('');
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
				$this->path = $host->substringFromPositionOfString('/');
				$this->port = 0;
				break;
			default:
				$this->scheme = $components->firstObject();
				$this->host = $components->objectForIndex(1)->removeLeadingCharactersWhenMatching('//');
				$portsAndPathComponents = $components->objectForIndex(2)->components('/');
				
				switch ($portsAndPathComponents->count()) {
					case 0:
						$this->port = 0;
						$this->path = __('');
						break;
					case 1:
						$this->port = (int)$portsAndPathComponents->firstObject()->integerValue();
						$this->path = __('');
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
	 * Adds a path to the URL.
	 * @param string $path
	 * @param bool $isDirectory
	 * @return \Poundation\PURL
	 */
	public function addPathComponent($path,$isDirectory = false) {
		$pathToUse = false;
		if (is_string($path)) {
			if (strlen($path) > 0) {
				$pathToUse = $path;
			}
		} else if ($path instanceof PString) {
			if ($path->length() > 0) {
				$pathToUse = $path->stringValue();
			}
		}
		
		if ($pathToUse !== false) {
			if (!$this->path->hasPrefix('/')) {
				$this->path->addString('/');
			}
			$this->path->addString($path);
			
			if ($isDirectory) {
				$this->path->ensureLastCharacter('/');
			} else {
				$this->path->removeTrailingCharactersWhenMatching('/');
			}
		}
		
		return $this;
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
	
	function setParameter($key,$value) {
		$usedKey = false;
		$usedValue = false;
		
		if (is_string($key)) {
			if (strlen($key) > 0) {
				$usedKey = $key;
			}
		} else if ($key instanceof PString) {
			if ($key->length() > 0) {
				$usedKey = $key->stringValue();
			}
		}
		
		if (is_string($value)) {
			if (strlen($value) > 0) {
				$usedValue = $value;
			}
		} else if ($value instanceof PString) {
			if ($value->length() > 0) {
				$usedValue = $value->stringValue();
			}
		}
		
		if ($key !== false && $value !== false) {
			
			$this->parameters[$key] = $value;
			
		} else {
			throw new Exception('key and value must be of type string (PString)');
		} 
		
		
	}
	
	function __toString() {
		
		$url = PString::createFromString($this->scheme())->addString('://');
		$url->addString($this->host());
		
		if ($this->scheme()->isEqual(PURL::SCHEME_HTTP)) {
			if ($this->port() != 80) {
				$url->addString(':')->addString((string)$this->port());		
			}
		} else if ($this->scheme() === PURL::SCHEME_HTTPS) {
			if ($this->port() != 443) {
				$url->addString(':')->addString((string)$this->port());		
			}
		}
		
		if ($this->path()->length() > 0) {
			$path = clone $this->path();
			$url->addString($path->ensureFirstCharacter('/'));	
		}
		
		if (count($this->parameters) > 0) {
			
			$usedParameters = array();
			foreach($this->parameters as $key=>$value) {
				
				$encodedKey = urlencode($key);
				$encodedValue = urlencode($value);
				$usedParameters[] = $encodedKey . '=' . $encodedValue;
			}
			$allParameters = implode('&', $usedParameters);
			$url->addString('?')->addString($allParameters);
		}
		
		return $url->stringValue();
		
	}
	
}

?>