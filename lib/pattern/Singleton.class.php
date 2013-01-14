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
namespace ikarus\pattern;
use ikarus\system\exception\StrictStandardException;

/**
 * For lazy programmers: Singleton
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class Singleton {

	/**
	 * Contains an instance of type Singelton
	 * @var			array<Singelton>
	 */
	protected static $instances = array();

	/**
	 * A protected construct method.
	 * @internal			Disallows the new operator on this class.
	 */
	protected function __construct() {
		$this->init();
	}

	/**
	 * Replaces the __construct() method.
	 * Note: You have to use this method to init own components
	 * @return			void
	 * @api
	 */
	public function init() { }

	/**
	 * A protected clone method.
	 * @return			void
	 * @internal			Prevents cloning of this class.
	 */
	protected function __clone() { }

	/**
	 * Disallows serialize().
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function __sleep() {
		throw new StrictStandardException("Instances of class %s are not serializable", __CLASS__);
	}

	/**
	 * Returnes an instance of Singelton
	 * @return		Singelton
	 * @api
	 */
	public static function getInstance() {
		// get class
		$className = get_called_class();

		// create instance if needed
		if(!array_key_exists($className, static::$instances)) static::$instances[$className] = new $className();

		// return instance
		return static::$instances[$className];
	}
}
?>