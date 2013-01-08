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
use ikarus\util\StringUtil;

/**
 * Parses and represents a response.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class Response {
	
	/**
	 * Defines the deflate encoding header.
	 * @var			string
	 */
	const ENCODING_DEFLATE = 'deflate';
	
	/**
	 * Defines the gzip encoding header.
	 * @var			string
	 */
	const ENCODING_GZIP = 'gzip';
	
	/**
	 * Stores the response body.
	 * @var			string
	 */
	protected $content = '';
	
	/**
	 * Stores a list of headers.
	 * @var			Header[]
	 */
	protected $headers = array();
	
	/**
	 * Stores the received status code.
	 * @var			integer
	 */
	protected $statusCode = 0;
	
	/**
	 * Stores the received status message.
	 * @var			string
	 */
	protected $statusMessage = '';
	
	/**
	 * Adds a new header to list.
	 * @param			Header			$header
	 * @return			void
	 */
	public function addHeader(Header $header) {
		$this->headers[] = $header;
	}
	
	/**
	 * Appends text to the response buffer.
	 * @param			string			$line
	 * @return			void
	 */
	public function append($content) {
		$this->content .= $content;
	}
	
	/**
	 * Decodes a compressed body.
	 * @param			string			$encodingType
	 * @return			void
	 * @throws			IOException
	 * @todo			Verify function. This may not work as PHP could use other compression levels or algorithms than Apache/the webserver.
	 */
	public function decodeBody($encodingType) {
		// validate encoding
		switch($encodingType) {
			case static::ENCODING_DEFLATE:
			case static::ENCODING_GZIP:
				if (!RequestBuilder::isCompressionAvailable()) throw new IOException('Cannot decode response body: Compression is not supported by PHP');
				break;
		}
		
		// decode
		switch($encodingType) {
			case static::ENCODING_DEFLATE:
				$this->content = gzdeflate($this->content);
				break;
			case static::ENCODING_GZIP:
				$this->content = gzdecode($this->content);
				break;
			default:
				throw new IOException('cannot decode response body: Unknown algorithm "%s" supplied', $encodingType);
				break;
		}
	}
	
	/**
	 * Parses an HTTP response.
	 * @param			string			$buffer
	 * @return			self
	 */
	public static function parse($buffer) {
		// unify newlines
		$buffer = StringUtil::unifyNewlines($buffer);
		
		// split buffer into lines
		$buffer = explode("\n", $buffer);
		
		// define state
		$inHeader = true;
		$response = new static();
		
		foreach($buffer as $lineNo => $line) {
			if ($inHeader) {
				if (rtrim($line) == '') {
					$inHeader = false;
					continue;
				}
				
				// parse first line
				if ($lineNo == 0) {
					// split
					list($httpHeader, $errorCode, $message) = explode(' ', $line, 3);
					
					// validate http header
					if ($httpHeader != RequestBuilder::HTTP_VERSION) throw new HTTPException('Protocol violation: Got wrong HTTP header "%s", expected "%s"', $httpHeader, RequestBuilder::HTTP_VERSION);
					
					// set data
					$response->setStatusCode(intval($errorCode));
					$response->setStatusMessage($message);
				}
			
				if (Header::isValid($line)) $response->addHeader(Header::parse($line));
			} else
				$response->appendLine($line);
		}
		
		// process headers
		foreach($response->getHeaders() as $header) {
			if ($header->getName() == 'Content-Encoding') $this->decodeBody($header->getValue());
		}
		
		return $response;
	}
	
	/**
	 * Returns the body of this response.
	 * @return			string
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Returns a list of headers.
	 * @return			\ikarus\system\io\http\Header[]
	 */
	public function getHeaders() {
		return $this->headers;
	}
	
	/**
	 * Returns the received status code.
	 * @return			integer
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	/**
	 * Returns the received status message.
	 * @return			string
	 */
	public function getStatusMessage() {
		return $this->statusMessage;
	}
	
	/**
	 * Sets a new status code.
	 * @param			integer			$code
	 * @return			void
	 */
	public function setStatusCode($code) {
		$this->statusCode = $code;
	}
	
	/**
	 * Sets a new status message.
	 * @param			string			$message
	 * @return			void
	 */
	public function setStatusMessage($message) {
		$this->statusMessage = $message;
	}
}
?>