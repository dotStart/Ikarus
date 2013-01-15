<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\io\protocol\http;
use ikarus\system\exception\io\http\HTTPException;

/**
 * Builds any kind of HTTP request.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class RequestBuilder {
	
	/**
	 * Defines the HTTP version to use.
	 * @var			string
	 */
	const HTTP_VERSION = 'HTTP/1.1';
	
	/**
	 * Defines the newline to use.
	 * Note: This is a Windows newline and should work on every server as it contains both \n and \r.
	 * @var			string
	 */
	const NEWLINE = "\r\n";
	
	/**
	 * Defines the delete type.
	 * @var			string
	 */
	const TYPE_DELETE = 'DELETE';
	
	/**
	 * Defines the get type.
	 * @var			string
	 */
	const TYPE_GET = 'GET';
	
	/**
	 * Defines the head type.
	 * @var			string
	 */
	const TYPE_HEAD = 'HEAD';
	
	/**
	 * Defines the options type.
	 * @var			string
	 */
	const TYPE_OPTIONS = 'OPTIONS';
	
	/**
	 * Defines the post type.
	 * @var			string
	 */
	const TYPE_POST = 'POST';
	
	/**
	 * Defines the put type.
	 * @var			string
	 */
	const TYPE_PUT = 'PUT';
	
	/**
	 * Defines the trace type.
	 * @var			string
	 */
	const TYPE_TRACE = 'TRACE';
	
	/**
	 * Stores a list of acceptables languages.
	 * @var			string[]
	 */
	protected $acceptableLanguages = array();
	
	/**
	 * Stores a list of acceptable mime types.
	 * @var			string[]
	 */
	protected $acceptableTypes = array();
	
	/**
	 * Stores the charset to use for communication.
	 * @var			string
	 */
	protected $charset = 'UTF-8';
	
	/**
	 * Stores a list of headers to send.
	 * @var			string[]
	 */
	protected $headers = array();
	
	/**
	 * Stores the hostname which we want to connect.
	 * @var			string
	 */
	protected $hostname = '';
	
	/**
	 * Stores the password which is used to connect.
	 * @var			string
	 */
	protected $password = null;
	
	/**
	 * Stores the path to query.
	 * @var			string
	 */
	protected $path = '/';
	
	/**
	 * Stores the port where we want to connect.
	 * @var			integer
	 */
	protected $port = 80;
	
	/**
	 * Stores the data to send.
	 * @var			string
	 */
	protected $postData = '';
	
	/**
	 * Stores a list of variables which we want to send.
	 * @var			string[]
	 */
	protected $queryVariables = array();
	
	/**
	 * Stores the scheme used for the connection (http or https).
	 * @var			string
	 */
	protected $scheme = 'http';
	
	/**
	 * Stores the current request type.
	 * @var			string
	 */
	protected $type = 'GET';
	
	/**
	 * Stores the current user agent template.
	 * @var			string
	 */
	protected $userAgent = 'Ikarus/%s (RequestBuilder)';
	
	/**
	 * Stores the username which is used to connect.
	 * @var			string
	 */
	protected $username = null;
	
	/**
	 * Stores a list of valid schemes.
	 * @var			string[]
	 */
	public static $validSchemes = array(
		'http',
		'https'
	);
	
	/**
	 * Stores a list of valid HTTP request types.
	 * @var			string[]
	 */
	public static $validTypes = array(
		static::TYPE_DELETE,
		static::TYPE_GET,
		static::TYPE_HEAD,
		static::TYPE_OPTIONS,
		static::TYPE_POST,
		static::TYPE_PUT,
		static::TYPE_TRACE
	);
	
	/**
	 * Adds an acceptable language to list.
	 * @param			string			$language
	 * @return			void
	 * @api
	 */
	public function addAcceptableLanguage($language) {
		$this->acceptableLanguages[] = $language;
	}
	
	/**
	 * Adds an acceptable type to list.
	 * @param			string			$type
	 * @return			void
	 * @api
	 */
	public function addAcceptableType($type) {
		$this->acceptableTypes[] = $type;
	}
	
	/**
	 * Adds a query variable.
	 * @param			string			$key
	 * @param			string			$value
	 * @return			void
	 * @api
	 */
	public function addQueryVariable($key, $value) {
		$this->queryVariables[$key] = $value;
	}
	
	/**
	 * Builds an accept header.
	 * @return			string
	 */
	protected function buildAcceptHeader() {
		// nothing set?
		if (!count($this->acceptableTypes)) return '*/*';
		
		// build acceptables string
		$acceptables = '';
		
		foreach($this->acceptableTypes as $type) {
			if (!empty($acceptables)) $acceptables .= ', ';
			$acceptables  .= $type;
		}
		
		return $acceptables;
	}
	
	/**
	 * Builds an accept language header.
	 * @return			string
	 */
	protected function buildAcceptLanguageHeader() {
		// nothing set?
		if (!count($this->acceptableLanguages)) return '*';
		
		// build acceptables string
		$acceptables = '';
		
		foreach($this->acceptableLanguages as $language) {
			if (!empty($acceptables)) $acceptables .= ', ';
			$acceptables .= $language;
		}
		
		return $acceptables;
	}
	
	/**
	 * Creates a new connection.
	 * @return			RemoteFile
	 */
	protected function createConnection() {
		return (new RemoteFile(($this->scheme == 'https' ? 'ssl://' : '').$this->hostname, $this->port));
	}
	
	/**
	 * Creates a request from URI.
	 * @param			string			$uri
	 * @return			self
	 * @api
	 */
	public static function fromURI($uri) {
		// parse url
		$uri = parse_url($uri);
		
		// create object
		$obj = new static();
		$obj->setPathFromURI($uri);
	}
	
	/**
	 * Indicates whether the compression is available.
	 * @return			boolean
	 * @api
	 */
	public static function isCompressionAvailable() {
		return (extension_loaded('zlib'));
	}
	
	/**
	 * Removes an acceptable language.
	 * @param			string			$language
	 * @return			void
	 * @api
	 */
	public function removeAcceptableLanguage($language) {
		// check
		if (!in_array($language, $this->acceptableLanguages)) return;
		
		foreach($this->acceptableLanguages as $key => $value) {
			if ($value == $language) unset($this->acceptableLanguages[$key]);
		}
	}
	
	/**
	 * Removes an acceptable type.
	 * @param			string			$type
	 * @return			void
	 * @api
	 */
	public function removeAcceptableType($type) {
		// check
		if (!in_array($type, $this->acceptableTypes)) return;
		
		foreach($this->acceptableTypes as $key => $value) {
			if ($value == $type) unset($this->acceptableTypes[$key]);
		}
	}
	
	/**
	 * Removes a header.
	 * @param			string			$key
	 * @return			void
	 * @api
	 */
	public function removeHeader($key) {
		if (!array_key_exists($key, $this->headers)) return;
		unset($this->headers[$key]);
	}
	
	/**
	 * Removes a query variable.
	 * @param			string			$key
	 * @return			void
	 * @api
	 */
	public function removeQueryVariable($key) {
		if (!array_key_exists($key, $this->queryVariables)) return;
		unset($this->queryVariables[$key]);
	}
	
	/**
	 * Resets the credentials (username and password).
	 * @return			void
	 * @api
	 */
	public function resetCredentials() {
		$this->username = null;
		$this->password = null;
	}
	
	/**
	 * Sets a new charset.
	 * @param			string			$charset
	 * @return			void
	 * @api
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}
	
	/**
	 * Sets the value of any kind of header.
	 * @param			string			$key
	 * @param			string			$value
	 * @return			void
	 * @api
	 */
	public function setHeader($key, $value) {
		$this->headers[$key] = $value;
	}
	
	/**
	 * Sets a new hostname.
	 * @param			string			$hostname
	 * @return			void
	 * @api
	 */
	public function setHostname($hostname) {
		$this->hostname = $hostname;
	}
	
	/**
	 * Sets a new password.
	 * @param			string			$password
	 * @return			void
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * Sets a new path.
	 * @param			string			$path
	 * @return			void
	 * @api
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	
	/**
	 * Sets all needed properties from URI.
	 * @param			string			$uri
	 * @return			void
	 * @api
	 */
	public function setPathFromURI($uri) {
		$uri = parse_url($uri);
		
		$this->setScheme((isset($uri['scheme']) ? $uri['scheme'] : 'http'));
		$this->setHostname($uri['host']);
		$this->setPort((isset($uri['port']) ? $uri['port'] : 80));
		if (isset($uri['user'])) $this->setUsername($uri['user']);
		if (isset($uri['pass'])) $this->setPassword($uri['pass']);
		$this->setPath((isset($uri['path']) ? $uri['path'] : '/'));
		if (isset($uri['query'])) {
			// parse query
			$variables = parse_str($uri['query']);
				
			foreach($variables as $key => $value) {
				$this->addQueryVariable($key, $value);
			}
		}
	}
	
	/**
	 * Sets a new port.
	 * @param			integer			$port
	 * @return			void
	 * @api
	 */
	public function setPort($port) {
		$this->port = $port;
	}
	
	/**
	 * Sets a new post string.
	 * @param			string			$data
	 * @return			void
	 * @api
	 */
	public function setPostData($data) {
		// build query array if needed
		if (is_array($data)) $data = http_build_query(data);
		
		// save
		$this->postData = $data;
	}
	
	/**
	 * Sets a new scheme.
	 * @param			string			$scheme
	 * @return			void
	 * @throws			HTTPException
	 * @api
	 */
	public function setScheme($scheme) {
		if (!in_array($scheme, static::$validSchemes)) throw new HTTPException('Cannot set request to unknown scheme "%s"', $scheme);
		$this->scheme = $scheme;
	}
	
	/**
	 * Sets a new type.
	 * @param			string			$type
	 * @return			void
	 * @throws			HTTPException
	 * @api
	 */
	public function setType($type) {
		if (!in_array($type, static::$validTypes)) throw new HTTPException('Cannot set request type to unknown type "%s"', $type);
		$this->type = $type;
	}
	
	/**
	 * Sets a new user agent.
	 * @param			string			$userAgent
	 * @return			void
	 * @api
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}
	
	/**
	 * Sets a new username.
	 * @param			string			$username
	 * @return			void
	 * @api
	 */
	public function setUsername($username) {
		$this->username = $username;
	}
	
	/**
	 * Builds the whole query.
	 * @return			void
	 * @api
	 */
	public function __toString() {
		// validation
		if (empty($this->hostname)) throw new HTTPException('Required parameter is missing: %s', 'hostname');
		
		// add header
		$request = $this->type.' '.$this->path.$this->buildQuery().' '.static::HTTP_VERSION.static::NEWLINE;
		$request .= 'User-Agent: '.sprintf($this->userAgent, IKARUS_VERSION).static::NEWLINE;
		$request .= 'Accept: '.$this->buildAcceptHeader().static::NEWLINE;
		$request .= 'Accept-Charset: '.$this->charset.static::NEWLINE;
		$request .= 'Accept-Language: '.$this->buildAcceptLanguageHeader().static::NEWLINE;
		$request .= 'Host: '.$this->hostname.static::NEWLINE;
		
		// auth
		if ($this->username !== null and $this->password !== null) $request .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password);
		
		// allow compression
		if (static::isCompressionAvailable()) $request .= 'Accept-Encoding: gzip, deflate'.static::NEWLINE;
		
		// append post information
		if (!empty($this->postData)) {
			$request .= 'Content-Type: application/x-www-form-urlencoded; charset='.$this->charset.static::NEWLINE;
			$request .= 'Content-Length: '.strlen($this->postData);
		}
		
		// add custom headers
		foreach($this->headers as $key => $value) {
			$request .= $key.': '.$value.static::NEWLINE;
		}
		
		// add spacer
		$request .= static::NEWLINE;
		
		// add post data (if any)
		$request .= $this->postData;
	}
}
?>