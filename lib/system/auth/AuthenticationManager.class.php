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

use ikarus\system\exception\StrictStandardException;

use ikarus\system\application\IApplication;
use ikarus\system\application\IConfigurableComponent;
use ikarus\system\database\QueryEditor;
use ikarus\system\event\IdentifierEventArguments;
use ikarus\system\exception\MissingDependencyException;
use ikarus\util\ClassUtil;
use ikarus\util\GUID;

/**
 * Allows low-level access to auth servers (Like an external Ikarus SSO server or a local database).
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class AuthenticationManager implements IConfigurableComponent {
	
	/**
	 * @see ikarus\util\ClassUtil::getDependencies()
	 */
	const DEPENDENCIES = 'ikarus\\system\\session\\SessionManager';
	
	/**
	 * Contains the current primary application.
	 * @var			ikarus\system\application.IApplication
	 */
	protected $application = null;
	
	/**
	 * Contains the current default adapter instance.
	 * @var			ikarus\system\auth.IAuthenticationAdapter
	 */
	protected $defaultAdapter = null;
	
	/**
	 * Contains a list of loaded adapters.
	 * @var			string[]
	 */
	protected $loadedAdapters = array();
	
	/**
	 * @see \ikarus\system\application\IConfigurableComponent::configure()
	 */
	public function configure(IApplication $application) {
		$this->application = $application;
		
		// load adapters
		$this->loadAdapters();
		$this->createConnections();
	}
	
	/**
	 * Loads all connections from database.
	 * @return			void
	 */
	public function createConnections() {
		$editor = new QueryEditor();
		$editor->from(array('ikarus1_auth_connection' => 'authConnection'));
		DependencyUtil::generateDependencyQuery($this->application->getPackageID(), $editor, 'authConnection');
		$stmt = $editor->prepare();
		foreach($stmt->fetchList() as $connection) {
			$linkID = $this->createInstance($connection->adapterID, $connection->linkID, (!empty($connection->parameters) ? unserialize($connection->parameters) : array()));
			if ($connection->isDefault) $this->setDefaultAdapter($this->getAdapter($linkID));
		}
	}
	
	/**
	 * Creates a new adapter instance.
	 * @param			integer			$adapterID
	 * @param			string			$linkID
	 * @param			array			$parameters
	 * @throws			MissingDependencyException
	 * @return			string
	 */
	public function createInstance($adapterID, $linkID = null, $parameters = array()) {
		// generate linkID (if not given)
		if ($linkID === null) $linkID = GUID::generate('AuthenticationManager');
		
		if (!$this->adapterLoaded($adapterID)) throw new MissingDependencyException('The adapter with ID "%u" is not loaded', $adapterID);
		if ($this->connectionExists($linkID)) throw new InvalidIdentifierException('LinkID "%s" exists already', $linkID);
		
		// construct object
		$instance = new $this->loadedAdapters[$adapterID]($parameters);
		
		// save instance
		$this->instances[$linkID] = $instance;
		
		// return linkID
		return $linkID;
	}
	
	/**
	 * Returns an adapter based on it's linkID.
	 * @param			string			$linkID
	 * @return			ikarus\system\auth\IAuthenticationAdapter
	 * @throws			StrictStandardException
	 */
	public function getAdapter($linkID) {
		// validate
		if (!array_key_exists($linkID, $this->instances)) throw new StrictStandardException('Cannot find authentication adapter with link ID "%s"', $linkID);
		
		return $this->instances[$linkID];
	}
	
	/**
	 * Returns the current default adapter maintained by this instance.
	 * @return			ikarus\system\auth\IAuthenticationAdapter
	 */
	public function getDefaultAdapter() {
		return $this->defaultAdapter;
	}
	
	/**
	 * Loads an adapter into memory.
	 * @param			integer			$adapterID
	 * @param			string			$className
	 * @throws			MissingDependencyException
	 * @return			boolean
	 */
	public function loadAdapter($adapterID, $className) {
		// fire cancellable event
		$event = new LoadAdapterEvent(new IdentifierEventArguments($className));
		Ikarus::getEventManager()->fire($event);
		
		// cancellable
		if ($event->isCancelled()) return false;
		
		// non-existing classes
		if (!ClassUtil::classExists($className, true)) throw new MissingDependencyException('Cannot find authentication adapter "%s"', $className);
		
		// validate class
		if (!ClassUtil::isInstanceOf($className, 'ikarus\\system\\auth\\adapter\\IAuthenticationAdapter')) throw new StrictStandardException('Authentication adapter "%s" does not inherit from base interface ikaurs\\system\\auth\\adapter\\IAuthenticationAdapter');
		
		// everythin' is fine, fire event
		Ikarus::getEventManager()->fire(new AdapterLoadedEvent(new IdentifierEventArguments($className)));
		
		// append to list
		$this->loadedAdapters[$adapterID] = $className;
		
		// ok!
		return true;
	}
	
	/**
	 * Loads all available adapters which are in application's path.
	 * @return			void
	 */
	public function loadAdapters() {
		$editor = new QueryEditor();
		$editor->from(array('ikarus1_auth_adapter' => 'authAdapter'));
		DependencyUtil::generateDependencyQuery($this->application->getPackageID(), $editor, 'authAdapter');
		$stmt = $editor->prepare();
		foreach($stmt->fetchList() as $adapter) {
			$this->loadAdapter($adapter->adapterID, $adapter->className);
		}
	}
	
	/**
	 * Sets the current default adapter.
	 * @param			IAuthenticationAdapter				$adapter
	 * @return			void
	 */
	public function setDefaultAdapter(IAuthenticationAdapter $adapter) {
		$this->defaultAdapter = $adapter;
	}
}
?>