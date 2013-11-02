<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\pattern;

use ikarus\system\exception\StrictStandardException;

/**
 * Non instantiable class pattern (Classes who inerhit from this will never have an instance)
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class NonInstantiableClass {

	/**
	 * A protected construct method.
	 * @internal                        This method is used to prevent the new operator from creating new instances.
	 */
	protected function __construct () {
	}

	/**
	 * A protected clone method.
	 * @internal                        This method prevents PHP from cloning existing instances of this class.
	 */
	protected function __clone () {
	}

	/**
	 * Disallows serialize().
	 * @throws                        StrictStandardException
	 * @return                        void
	 * @internal                        This method prevents developers from serializing instances of this class.
	 */
	public function __sleep () {
		throw new StrictStandardException("Instances of class %s are not serializable", __CLASS__);
	}
}

?>