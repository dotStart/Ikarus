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
namespace ikarus;

	/**
	 * @author                    Johannes Donath
	 * @copyright                 2011 Evil-Co.de
	 * @package                   de.ikarus-framework.core
	 * @subpackage                system
	 * @category                  ikarus\system\Ikarus Framework
	 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
	 * @version                   1.0.0-0001
	 */

// set exception handler
set_exception_handler (array('ikarus\system\Ikarus', 'handleException'));

// set error handler
set_error_handler (array('ikarus\system\Ikarus', 'handleError'), E_ALL);

// register autoloader
spl_autoload_register (array('ikarus\system\Ikarus', 'autoload'));

// register shutdown method
register_shutdown_function (array('ikarus\system\Ikarus', 'shutdown'));

// assert settings
assert_options (ASSERT_WARNING, false);
assert_options (ASSERT_CALLBACK, array('ikarus\system\Ikarus', 'handleAssertion'));
?>