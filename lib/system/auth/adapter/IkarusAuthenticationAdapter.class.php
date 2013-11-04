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
namespace ikarus\system\auth\adapter;

use ikarus\system\application\IApplication;
use ikarus\system\auth\IAuthenticationAdapter;
use ikarus\system\auth\IGenericAuthenticationAdapter;
use ikarus\system\Ikarus;
use ikarus\util\DependencyUtil;
use ikarus\util\HashUtil;

/**
 * Implements a default adapter which implements a default database-based auth backend.
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class IkarusAuthenticationAdapter implements IAuthenticationAdapter, IGenericAuthenticationAdapter {

	/**
	 * Defines which object represents newly created user objects.
	 * @var                        IUserObject
	 */
	const USER_OBJECT = 'ikarus\\data\\user\\User';

	/**
	 * Contains the parent application.
	 * @var                        ikarus\system\application.IApplication
	 */
	protected $application = null;

	/**
	 * @see                        ikarus\system\auth.IAuthenticationAdapter
	 */
	public function __construct (IApplication $application, $parameters = array ()) {
		$this->application = $application;
	}

	/**
	 * @see \ikarus\system\auth\IGenericAuthenticationAdapter::auth()
	 */
	public function auth ($username = null, $password = null) {
		$query = new QueryEditor();
		$query->from (array ('ikarus1_user', 'user'));
		$query->where ("user.username = ?");
		if (!Ikarus::getConfiguration ()->get ('application.user.ignoreInstanceBounds')) DependencyUtil::generateInstanceQuery ($this->application->getInstanceID (), $query, 'user');
		$stmt = $query->prepare ();
		$stmt->bind ($username);
		$user = $stmt->fetch ();

		// no user found
		if (!$user) return false;

		// validate password
		if (HashUtil::generateSaltedHash ($password, $user->securitySalt) === $user->hashedPassword) {
			$userObject = static::USER_OBJECT; // Workaround ... This sucks.

			$session->setVariable ('user', new $userObject($user));

			return true;
		}

		return false;
	}

	/**
	 * @see \ikarus\system\auth\IAuthenticationAdapter::getUserObject()
	 */
	public function getUserObject (ikarus\system\session\ISession $session = null) {
		if ($session === null) $session = Ikarus::getSessionManager ()->getSession ($this->application->getAbbreviation ());

		return $session->getUser ();
	}

	/**
	 * @see \ikarus\system\auth\IAuthenticationAdapter::isAuthenticated()
	 */
	public function isAuthenticated (ikarus\system\session\ISession $session = null) {
		if ($session === null) $session = Ikarus::getSessionManager ()->getSession ($this->application->getAbbreviation ());

		return ($session->getUser () !== null);
	}

	/**
	 * @see \ikarus\system\auth\IAuthenticationAdapter::isSupported()
	 */
	public static function isSupported () {
		return true; // This adapter is supported on every system (hopefully)
	}
}

?>