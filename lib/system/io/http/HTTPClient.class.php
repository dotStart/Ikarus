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

use ikarus\system\exception\io\http\HTTPException;
use ikarus\system\exception\io\http\ConnectionException;
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
	 * Executes a request.
	 * @param			RequestBuilder			$request
	 * @return			\ikarus\system\io\http\Response
	 * @throws			ConnectionException
	 */
	public static function executeQuery(RequestBuilder $request) {
		// get connection
		$connection = $request->createConnection();
		
		// validate connection
		if (!$connection) throw new ConnectionException('Cannot establish connection to server "%s"', $urlInformation['host']);
		
		// send request
		$connection->puts($request->__toString());
		
		// init read array
		$buffer = '';
			
		// read http response.
		while (!$connection->eof()) {
			// append contents
			$buffer .= $connection->gets();
		}
		
		// close remote file
		$connection->close();
		
		// parse response
		$response = Response::parse($buffer);
		
		// check for redirects
		if ($response->getStatusCode() == 302 or $response->getStatusCode() == 307) {
			// get location field
			$locationHeader = null;
			
			foreach($response->getHeaders() as $header) {
				if ($header->getName() == 'Location') $locationHeader = $header;
			}
			
			// validation
			if ($locationHeader === null) throw new HTTPException('Protocol violation: Got status code %u but there is no location header present', $response->getStatusCode());
			
			// change request and resend
			$request->setPathFromURI($locationHeader->getValue());
			
			return static::executeQuery($request);
		}
		
		return $response;
	}
	
	/**
	 * Sends a GET request to specified URI.
	 * @param			string			$URI
	 * @throws			ConnectionException
	 * @throws			HTTPException
	 * @return			string
	 */
	public static function get($URI) {
		// init request
		$request = RequestBuilder::fromURI($uri);
		$request->setMethod(RequestBuilder::TYPE_GET);
		
		// execute query
		return static::executeQuery($request);
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
		// init request
		$request = RequestBuilder::fromURI($uri);
		$request->setMethod(RequestBuilder::TYPE_POST);
		$request->setPostData($data);
		
		// execute query
		return static::executeQuery($request);
	}
}
?>