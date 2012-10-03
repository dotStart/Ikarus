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
namespace ikarus\system\io\http;

use ikarus\system\Ikarus;

/**
 * Allows low-level access to HTTP servers.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class HTTPClient {
	
	/**
	 * Defines the used HTTP version.
	 * @var			string
	 */
	const HTTP_VERSION = '1.1';
	
	/**
	 * Defines the used newline (Windows compatible).
	 * @var			string
	 */
	const NEWLINE = "\r\n";
	
	/**
	 * Defines a DELETE request.
	 * @var			string
	 */
	const REQUEST_DELETE = 'DELETE';
	
	/**
	 * Defines a GET request.
	 * @var			string
	 */
	const REQUEST_GET = 'GET';
	
	/**
	 * Defines an OPTIONS request.
	 * @var			string
	 */
	const REQUEST_OPTIONS = 'OPTIONS';
	
	/**
	 * Defines a POST request.
	 * @var			string
	 */
	const REQUEST_POST = 'POST';
	
	/**
	 * Defines a PURGE request.
	 * @var			string
	 */
	const REQUEST_PURGE = 'PURGE';
	
	/**
	 * Defines a PUT request.
	 * @var			string
	 */
	const REQUEST_PUT = 'PUT';
	
	/**
	 * Defines a list of valid request operations.
	 * @var			string[]
	 */
	protected static $validRequestTypes = array(
		static::REQUEST_DELETE,
		static::REQUEST_GET,
		static::REQUEST_OPTIONS,
		static::REQUEST_POST,
		static::REQUEST_PURGE,
		static::REQUEST_PUT
	);
	
	/**
	 * Builds a request string (HTTP-Syntax)
	 * @param			string			$type
	 * @param			string			$URI
	 * @param			string[]		$data
	 */
	public static function buildRequestString($type, $URI, $data = array()) {
		// validate type
		if (!in_array($type, static::$validRequestTypes)) throw new HTTPException('Invalid request type "%s" supplied to '.__CLASS__.'::'.__FUNCTION__);
		
		// parse URI
		$parsedURI = static::parseURI($URI);
		
		// encode data
		$dataString = http_build_query($data);
		
		// build query
		$request = $type.' '; // type
		$request .= $parsedURI['path']; // main path
		$request .= (!empty($parsedURI['query']) ? '?'.$parsedURI['query'] : ''); // query
		$request .= 'HTTP/'.static::HTTP_VERSION.static::NEWLINE;  // HTTP-Version
		$request .= 'User-Agent: '.sprintf(static::USER_AGENT, IKARUS_VERSION).';'; // User-Agent
		$request .= (Ikarus::componentAbbreviationExists('LanguageManager') ? ' '.Ikarus::getLanguageManager()->getActiveLanguage()->getLanguageCode() : '').static::NEWLINE; // language code
		$request .= 'Accept: */*'.static::NEWLINE; // accept everything
		$request .= 'Accept-Language: '.(Ikarus::componentAbbreviationExists('LanguageManager') ? Ikarus::getLanguageManager()->getActiveLanguage()->getLanguageCode() : 'en-US').static::NEWLINE; // Accept language
		$request .= 'Host: '.$parsedURI['host'].static::NEWLINE; // Hostname
		if (!empty($data)) { // Support for post-data
			$request .= 'Content-Type: application/x-www-form-urlencoded'.static::NEWLINE; // content type
			$request .= 'Content-Length: '.strlen($dataString).static::NEWLINE; // add content length
		}
		$request .= 'Connection: Close'.static::NEWLINE.static::NEWLINE;
		
		// append data
		if (!empty($data)) $request .= $dataString;
		
		// finished building
		return $request;
	}
	
	/**
	 * Sends a GET request to specified URI.
	 * @param			string			$URI
	 * @throws			ConnectionException
	 * @throws			HTTPException
	 * @return			string
	 */
	public static function get($URI) {
		// init buffer
		$buffer = "";
		
		// get proxy
		$options = array();
		if (Ikarus::getConfiguration()->get('global.advanced.httpProxy')) $options['http']['proxy'] = Ikarus::getConfiguration()->get('global.advanced.httpProxy');
		
		// parse URI
		$urlInformation = static::parseURI($URI);
		
		// build request URI
		$request = static::buildRequestString(static::REQUEST_GET, $URI);
		
		// create connection
		$server = static::createConnection($urlInformation);
		
		// validate connection
		if (!$server) throw new ConnectionException('Cannot establish connection to server "%s"', $urlInformation['host']);
		
		// send request
		$remoteFile->puts($request);
		
		// waiting for response
		$waiting = true;
		
		// init read array
		$readResponse = array();
			
		// read http response.
		while (!$remoteFile->eof()) {
			// append contents
			$readResponse[] = $remoteFile->gets();
	
			// look if we are done with transferring the requested file.
			if ($waiting)
				// got a response?
				if (rtrim($readResponse[count($readResponse) - 1]) == '') $waiting = false;
			else {
				// look if the webserver sent an error http statuscode
				// This has still to be checked if really sufficient!
				$arrayHeader = array('201', '301', '302', '303', '307', '404');
				
				// get error headers
				foreach ($arrayHeader as $code) {
					$error = strpos($readResponse[0], $code);
				}
				
				// check for errors
				if ($error !== false) throw new HTTPException('Cannot read file "%s" at host "%s"', $urlInformation['path'], $urlInformation['host']);
					
				// write to the target system.
				$buffer .= $readResponse[count($readResponse) - 1];
			}
		}
		
		// close remote file
		$remoteFile->close();
		
		// return filename
		return $buffer;
	}
	
	/**
	 * Wrapper for parse_url()
	 * @see			parse_url()
	 */
	public static function parseURI($URI) {
		return parse_url($URI);
	}
	
	/**
	 * Sends a POST request to specified URI.
	 * @param			string			$URI
	 * @param			string[]		$data
	 * @throws			ConnectionException
	 * @throws			HTTPException
	 * @return			string
	 */
	public static function post($URI, $data = array()) {
		// init buffer
		$buffer = "";
		
		// get proxy
		$options = array();
		if (Ikarus::getConfiguration()->get('global.advanced.httpProxy')) $options['http']['proxy'] = Ikarus::getConfiguration()->get('global.advanced.httpProxy');
		
		// parse URI
		$urlInformation = static::parseURI($URI);
		
		// build request URI
		$request = static::buildRequestString(static::REQUEST_POST, $URI, $data);
		
		// create connection
		$server = static::createConnection($urlInformation);
		
		// validate connection
		if (!$server) throw new ConnectionException('Cannot establish connection to server "%s"', $urlInformation['host']);
		
		// send request
		$remoteFile->puts($request);
		
		// waiting for response
		$waiting = true;
		
		// init read array
		$readResponse = array();
			
		// read http response.
		while (!$remoteFile->eof()) {
			// append contents
			$readResponse[] = $remoteFile->gets();
		
			// look if we are done with transferring the requested file.
			if ($waiting)
				// got a response?
				if (rtrim($readResponse[count($readResponse) - 1]) == '') $waiting = false;
			else {
				// look if the webserver sent an error http statuscode
				// This has still to be checked if really sufficient!
				$arrayHeader = array('201', '301', '302', '303', '307', '404');
		
				// get error headers
				foreach ($arrayHeader as $code) {
					$error = strpos($readResponse[0], $code);
				}
		
				// check for errors
				if ($error !== false) throw new HTTPException('Cannot read file "%s" at host "%s"', $urlInformation['path'], $urlInformation['host']);
					
				// write to the target system.
				$buffer .= $readResponse[count($readResponse) - 1];
			}
		}
		
		// close remote file
		$remoteFile->close();
		
		// return filename
		return $buffer;
	}
}
?>