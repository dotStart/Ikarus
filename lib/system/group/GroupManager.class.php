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
namespace ikarus\system\group;

use ikarus\data\user\User;
use ikarus\system\application\IApplication;
use ikarus\system\application\IConfigurableComponent;

/**
 * Manages groups and permissions.
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class GroupManager implements IConfigurableComponent {

	/**
	 * Contains the current application instance.
	 * @var                        ikarus\system\application.IApplication
	 */
	protected $application = null;

	/**
	 * Contains all group handles.
	 * @var                        array<ikarus\data\group.Group>
	 */
	protected $groups = array();

	/**
	 * Contains a list of groups mapped to a user.
	 * @var                        array<ikarus\system\group\UserPermissionHandle>
	 */
	protected $userToGroupCache = array();

	/**
	 * Boots up the component.
	 * @return                        void
	 * @api
	 */
	public function boot () {
		// load cache
		$this->loadCache ();
	}

	/**
	 * @see ikarus\system\application.IConfigurableComponent::configure()
	 */
	public function configure (IApplication $application) {
		$this->application = $application;

		// boot up
		$this->boot ();
	}

	/**
	 * Returns a cached group handle.
	 * @param                        User $user
	 * @return                        UserGroupHandle
	 * @api
	 */
	public function getGroupHandle (User $user) {
		// get group IDs
		$groupIDs = $user->getGroupIDs ();

		// load cache
		$cacheIdentifier = 'groups-' . sha1 (implode (',', $groupIDs)) . '-' . $this->application->getPackageID ();
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ($cacheIdentifier, $cacheIdentifier, 'ikarus\\system\\cache\\builder\\CacheBuilderUserGroupHandle', array('groupIDs' => $groupIDs));

		return Ikarus::getCacheManager ()->getDefaultAdapter ()->get ($cacheIdentifier);
	}

	/**
	 * Loads all needed caches.
	 * @return                        void
	 */
	protected function loadCache () {
		// get groups
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ('groups-' . $this->application->getPackageID (), 'groups-' . $this->application->getPackageID (), 'ikarus\\system\\cache\\builder\\CacheBuilderGroups');
		$this->groups = Ikarus::getCacheManager ()->getDefaultAdapter ()->get ('groups-' . $this->application->getPackageID ());
	}
}

?>