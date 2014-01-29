<?php
namespace Poundation;

class PURL extends PObject implements \JsonSerializable {

	const SCHEME_HTTP  = 'http';
	const SCHEME_HTTPS = 'https';

	private $fullyConstructed;

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
	 *
	 * @param $urlString
	 *
	 * @return null|PURL
	 */
	public static function URLWithString( $urlString ) {
		$url = new self( $urlString );
		if ( $url->host()->length() == 0 ) {
			return null;
		} else {
			return $url;
		}
	}

	/**
	 * Checks if a given string has the structure of an URL.
	 *
	 * @param $urlString
	 *
	 * @return bool
	 */
	public static function isValidURLString( $urlString ) {
		$url = self::URLWithString( $urlString );

		return ( $url instanceof PURL && $url->fullyConstructed );
	}

	public static function currentURL() {

		$URL = new self( $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"] );
		$URL->setScheme( ( $_SERVER["HTTPS"] == "on" ) ? self::SCHEME_HTTPS : self::SCHEME_HTTP );

		return $URL;
	}

	/**
	 *  Creates a new PURL object with the given string-based URL.
	 *
	 * @return PURL
	 */
	function __construct( $URLString = null ) {
		if ( ! $URLString instanceof PString ) {
			$URLString = new PString( $URLString );
		}

		if ( $URLString->length() > 0 ) {
			if ( ! $URLString->hasPrefix( 'http' ) ) {
				$URLString = $URLString->prependString( 'http://' );
			}
		}

		$this->URLString        = $URLString;
		$components             = $this->URLString->components( ':' );
		$this->fullyConstructed = false;
		switch ( $components->count() ) {
			case 0:
				$this->scheme = new PString( '' );
				$this->host   = new PString( '' );
				$this->port   = 0;
				break;
			case 1:
				$this->scheme = new PString( self::SCHEME_HTTP );
				$this->host   = $components->firstObject()->removeLeadingCharactersWhenMatching( '//' );
				$this->port   = 0;
				break;
			case 2:
				$this->scheme           = $components->firstObject();
				$host                   = $components->lastObject()->removeLeadingCharactersWhenMatching( '//' );
				$this->host             = $host->substringToPositionOfString( '/' );
				$this->path             = $host->substringFromPositionOfString( '/' );
				$this->port             = 0;
				$this->fullyConstructed = true;
				break;
			default:
				$this->scheme           = $components->firstObject();
				$this->host             = $components->objectForIndex( 1 )->removeLeadingCharactersWhenMatching( '//' );
				$portsAndPathComponents = $components->objectForIndex( 2 )->components( '/' );

				switch ( $portsAndPathComponents->count() ) {
					case 0:
						$this->port = 0;
						$this->path = new PString( '' );
						break;
					case 1:
						$this->port = (int) $portsAndPathComponents->firstObject()->integerValue();
						$this->path = new PString( '' );
						break;
					default:
						$this->port = $portsAndPathComponents->firstObject()->integerValue();
						unset( $portsAndPathComponents[0] );
						$this->path = $portsAndPathComponents->string( '/' );
						break;
				}

				$this->fullyConstructed = true;

				break;
		}

		if ( $this->path()->contains( '?' ) ) {
			$pathComponent      = $this->path()->substringToPositionOfString( '?' );
			$parameterComponent = $this->path()->substringFromPositionOfString( '?' );

			$this->setPath( $pathComponent );

			$parameters = $parameterComponent->components( '&' );
			foreach ( $parameters as $parameter ) {
				if ( $parameter instanceof PString ) {
					$parameterParts = $parameter->components( '=' );
					switch ( $parameterParts->count() ) {
						case 2:
							$this->setParameter( $parameterParts[0], $parameterParts[1] );
							break;
						case 1:
							$this->setParameter( $parameterParts[0], null );
							break;
						case 0:
						default:
							break;
					}
				}
			}
		}

		if ( $this->port == 0 ) {
			if ( $this->scheme == self::SCHEME_HTTP ) {
				$this->port = 80;
			} else {
				if ( $this->port == self::SCHEME_HTTPS ) {
					$this->port = 443;
				}
			}
		}
	}

	/**
	 * Returns the scheme of the request.
	 *
	 * @return \Poundation\PString
	 */
	public function scheme() {
		return $this->scheme;
	}

	/**
	 * Sets the scheme of the URL.
	 *
	 * @param $scheme
	 *
	 * @return PURL
	 */
	public function setScheme( $scheme ) {
		$this->scheme = new PString( $scheme );

		return $this;
	}

	/**
	 * Returns the host of the request.
	 *
	 * @return \Poundation\PString
	 */
	public function host() {
		return $this->host;
	}

	/**
	 * Sets the host of the URL.
	 *
	 * @param $host
	 *
	 * @return PURL
	 */
	public function setHost( $host ) {
		$this->host = new PString( $host );

		return $this;
	}

	/**
	 * Returns the port of the request.
	 *
	 * @return integer
	 */
	public function port() {
		return $this->port;
	}

	/**
	 * Sets the port of the URL.
	 *
	 * @param $port
	 *
	 * @return PURL
	 */
	public function setPort( $port ) {
		$this->port = $port;

		return $this;
	}

	/**
	 * Returns the path of the URL.
	 *
	 * @return \Poundation\PString
	 */
	public function path() {
		return $this->path;
	}

	/**
	 * Returns the path components as array.
	 *
	 * @return null|PArray
	 */
	public function pathComponents() {
		$components = null;

		if ( $this->path instanceof PString ) {
			$components = $this->path->components( '/' );
		}

		return $components;
	}

	/**
	 * Sets the path of the URL.
	 *
	 * @param $path
	 *
	 * @return PURL
	 */
	public function setPath( $path ) {
		$this->path = new PString( $path );

		return $this;
	}

	/**
	 * Adds a path to the URL.
	 *
	 * @param string $path
	 * @param bool   $isDirectory
	 *
	 * @return \Poundation\PURL
	 */
	public function addPathComponent( $path, $isDirectory = false ) {
		$pathToUse = false;
		if ( is_string( $path ) ) {
			if ( strlen( $path ) > 0 ) {
				$pathToUse = PString::createFromString( $path );
			}
		} else {
			if ( $path instanceof PString ) {
				if ( $path->length() > 0 ) {
					$pathToUse = $path;
				}
			}
		}

		if ( is_null( $this->path ) ) {
			$this->path = new PString( '' );
		}

		if ( $pathToUse !== false ) {
			if ( ! $this->path->hasSuffix( '/' ) && ! ( $pathToUse->hasPrefix( '/' ) ) ) {
				$this->path->addString( '/' );
			}
			$this->path->addString( $path );

			if ( $isDirectory ) {
				$this->path->ensureLastCharacter( '/' );
			} else {
				$this->path->removeTrailingCharactersWhenMatching( '/' );
			}
		}

		return $this;
	}

	/**
	 * Returns a list of components of the URL.
	 *
	 * @return \Poundation\PArray
	 */
	function domainComponents() {
		return $this->host->components( '.' );
	}

	/**
	 * Returns a string with the top-level domain.
	 *
	 * @return \Poundation\PString
	 */
	function domain() {
		$components         = $this->domainComponents();
		$numberOfComponents = $components->count();
		switch ( $numberOfComponents ) {
			case 0:
				return null;
				break;
			case 1:
				return new PString( $components->objectForIndex( 0 ) );
				break;
			case 2:
				return new PString( $components->string( '.' ) );
				break;
			default:
				return PString::createFromString( $components[$numberOfComponents - 2] )->appendString( '.' )->appendString( $components[$numberOfComponents - 1] );
				break;
		}
	}

	/**
	 * Returns an array with all subdomains.
	 *
	 * @return \Poundation\PArray
	 */
	function subdomains() {
		$subdomains = new PArray();

		$components         = $this->domainComponents();
		$numberOfComponents = $components->count();
		if ( $numberOfComponents >= 3 ) {
			for ( $i = 0; $i < $numberOfComponents - 2; $i ++ ) {
				$subdomains->add( $components->objectForIndex( $i ) );
			}
		}

		return $subdomains;
	}

	function setParameter( $key, $value ) {
		$usedKey   = false;
		$usedValue = false;

		if ( is_string( $key ) ) {
			if ( strlen( $key ) > 0 ) {
				$usedKey = $key;
			}
		} else {
			if ( $key instanceof PString ) {
				if ( $key->length() > 0 ) {
					$usedKey = $key->stringValue();
				}
			}
		}

		if ( is_string( $value ) ) {
			if ( strlen( $value ) > 0 ) {
				$usedValue = $value;
			}
		} else {
			if ( $value instanceof PString ) {
				if ( $value->length() > 0 ) {
					$usedValue = $value->stringValue();
				}
			}
		}

		if ( $key !== false && $value !== false ) {

			$this->parameters[$usedKey] = $usedValue;

		} else {
			throw new Exception( 'key and value must be of type string (PString)' );
		}

	}

	function __toString() {

		$url = PString::createFromString( $this->scheme() )->addString( '://' );
		$url->addString( $this->host() );

		if ( $this->scheme()->isEqual( PURL::SCHEME_HTTP ) ) {
			if ( $this->port() != 80 ) {
				$url->addString( ':' )->addString( (string) $this->port() );
			}
		} else {
			if ( $this->scheme() === PURL::SCHEME_HTTPS ) {
				if ( $this->port() != 443 ) {
					$url->addString( ':' )->addString( (string) $this->port() );
				}
			}
		}

		if ( $this->path instanceof PString && $this->path()->length() > 0 ) {
			$path = clone $this->path();
			$url->addString( $path->ensureFirstCharacter( '/' ) );
		}

		if ( count( $this->parameters ) > 0 ) {

			$usedParameters = array();
			foreach ( $this->parameters as $key => $value ) {

				$encodedKey = urlencode( $key );
				if ( $value ) {
					$encodedValue     = urlencode( $value );
					$usedParameters[] = $encodedKey . '=' . $encodedValue;
				} else {
					$usedParameters[] = $encodedKey;
				}
			}
			$allParameters = implode( '&', $usedParameters );
			$url->addString( '?' )->addString( $allParameters );
		}

		return $url->stringValue();
	}

	/**
	 * (PHP 5 >= 5.4.0)
	 * Serializes the object to a value that can be serialized natively by json_encode().
	 *
	 * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
	 */
	function jsonSerialize() {
		return $this->__toString();
	}

}

?>