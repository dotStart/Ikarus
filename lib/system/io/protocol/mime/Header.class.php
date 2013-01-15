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
namespace ikarus\system\io\protocol\mime;

/**
 * Represents an HTTP header.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class Header {
	
	/**
	 * Stores the header name.
	 * @var				string
	 */
	protected $name = '';
	
	/**
	 * Stores the header value.
	 * @var				string
	 */
	protected $values = '';
	
	/**
	 * Constructs the object.
	 * @param			string			$name
	 * @param			string			$value
	 * @internal			Header instances should get created thru Header::parse()
	 */
	public function __construct($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}
	
	/**
	 * Returns the header name.
	 * @return			string
	 * @api
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns the header value.
	 * @return			string
	 * @api
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Checks whether a string is a valid HTTP header.
	 * @return			boolean
	 * @api
	 */
	public static function isValid($line) {
		return (strpos($line, ':') !== -1);
	}
	
	/**
	 * Parses an HTTP header line.
	 * @param			string			$line
	 * @return			self
	 * @api
	 */
	public static function parse($line) {
		// remove whitespace characters
		$line = rtrim($line);
		
		// explode
		list($name, $value) = explode(':', $line, 2);
		
		return (new Header($name, $value));
	}
	
	/**
	 * Converts the header back to a mime header string.
	 * @return			string
	 */
	public function __toString() {
		return $this->name.': '.$this->value;
	}
}
?>