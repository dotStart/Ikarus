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
namespace ikarus\system\event;

/**
 * The base class for all event arguments.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
use ikarus\system\exception\StrictStandardException;

abstract class AbstractEventArguments implements IEventArguments {
	
	/**
	 * Contains the content of this argument list.
	 * @var	array
	 */
	protected $content = array();
	
	/**
	 * Constructs the object.
	 * @param			array			$content
	 */
	public function __construct($content) {
		$this->content = $content;
	}
	
	/**
	 * Returns the value of a specific variable.
	 * @param			string			$variable
	 * @throws			StrictStandardException
	 */
	public function __get($variable) {
		if ($this->_isset($variable)) return $this->content[$variable];
		throw new StrictStandardException('Cannot access non-existant variable "%s" in class "%s"', $variable, get_class($this));
	}
	
	/**
	 * Checks whether a variable exists or not.
	 * @param			string			$variable
	 */
	public function __isset($variable) {
		return array_key_exists($variable, $this->content);
	}
}
?>