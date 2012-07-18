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
 * Non instantiable class pattern (Classes who inerhit from this will never have an instance)
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class NonInstantiableClass {
	
	/**
	 * A protected construct method
	 */
	protected function __construct() { }
	
	/**
	 * A protected clone method
	 */
	protected function __clone() { }
	
	/**
	 * Disallows serialize()
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function __sleep() {
		throw new StrictStandardException("It's not allowed to serialize singletons");
	}
}
?>