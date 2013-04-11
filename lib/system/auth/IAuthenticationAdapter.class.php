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
namespace ikarus\system\auth;

/**
 * Defines needed public methods for auth adapters.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IAuthenticationAdapter {
	
	/**
	 * Constructs the object.
	 * @param			mixed[]			$parameters
	 * @internal			New instances are created by Ikarus ...
	 */
	public function __construct($parameters);
	
	/**
	 * Returns a user object (if any).
	 * @param			ikarus\system\session\ISession			$session
	 * @return			ikarus\system\auth\IUserObject
	 * @api
	 */
	public function getUserObject(ikarus\system\session\ISession $session = null);
	
	/**
	 * Checks whether the current session belongs to an authenticated user.
	 * @return			boolean
	 * @api
	 */
	public function isAuthenticated(ikarus\system\session\ISession $session = null);
	
	/**
	 * Checks whether this adapter is supported in current environment.
	 * @return			boolean
	 * @api
	 */
	public static function isSupported();
}
?>