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
namespace ikarus\system\database;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;

/**
 * Manages all database connections
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DatabaseManager {

	/**
	 * Contains the path to dir where all database adapters are stored
	 * @var		string
	 */
	const ADAPTER_DIR = 'lib/system/database/adapter/';

	/**
	 * Contains the namespace wich contains all adapters
	 * @var		string
	 */
	const ADAPTER_NAMESPACE = 'ikarus\system\database\adapter';

	/**
	 * Contains the suffix for database adapter classes
	 * @var		string
	 */
	const ADAPTER_SUFFIX = 'DatabaseAdapter';

	/**
	 * Contains the name of the file that stores default database connection information
	 * @var		string
	 */
	const DATABASE_CONFIGURATION_FILE = 'config.inc.php';

	/**
	 * Contains all active application connections.
	 * @var			array<ikarus\system\database\adapter\IDatabaseAdapter>
	 */
	protected $applicationConnections = array();

	/**
	 * Contains all active database connections
	 * @var			array<ikarus\system\database\adapter\IDatabaseAdapter>
	 */
	protected $connections = array();

	/**
	 * Contains the adapter handle used by default
	 * @var			ikarus\system\database\adapter\IDatabaseAdapter
	 */
	protected $defaultAdapter = null;

	/**
	 * Contains a list of loaded adapters
	 * @var		array<string>
	 */
	protected $loadedAdapters = array();

	/**
	 * Contains a list of needed configuration variables defined in configuration file (See above for file path)
	 * @var		array<string>
	 */
	protected $neededConfigurationVariables = array(
		'adapterName',	'hostname',
		'port',		'user',
		'password',	'databaseParameters',
		'charset',	'databaseName'
	);

	/**
	 * Returnes true if the specified adapter is already loaded
	 * @param			string		$adapterName
	 * @return			boolean
	 */
	public function adapterIsLoaded($adapterName) {
		return in_array($adapterName, $this->loadedAdapters);
	}

	/**
	 * Creates a new connection with given parameters
	 * @param			string			$adapterName
	 * @param			string			$hostname
	 * @param			integer			$port
	 * @param			string			$user
	 * @param			string			$password
	 * @param			string			$databaseParameters
	 * @throws			SystemException
	 * @return			ikarus\system\database\adapter\IDatabaseAdapter
	 */
	public function createConnection($adapterName, $hostname, $port, $user, $password, $databaseParameters = '', $linkID = null) {
		// validate adapter
		if (!$this->adapterIsLoaded($adapterName)) throw new SystemException("Cannot create database connection with adapter '%s': The given adapter is currently not loaded", $adapterName);

		// validate linkID
		if (isset($this->connections[$linkID])) throw new SystemException("A connection with identifier '%s' does already exist", $linkID);

		// generate class name
		$className = static::ADAPTER_NAMESPACE.'\\'.$adapterName.static::ADAPTER_SUFFIX;

		// create instance
		$handle = new $className($hostname, $port, $user, $password, $databaseParameters);

		// save with link ID
		if ($linkID !== null) $this->connections[$linkID] = $handle;

		return $this->connections[] = $handle;
	}

	/**
	 * Returns the connection with given link identifier
	 * @param			string				$linkID
	 * @throws			SystemException
	 * @return			ikarus\system\database\adapter\IDatabaseAdapter
	 */
	public function getConnection($linkID) {
		// validate linkID
		if (!isset($this->connections[$linkID])) throw new SystemException("A connection with identifier '%s' does not exist", $linkID);

		return $this->connections[$linkID];
	}

	/**
	 * Returns the current default connection
	 * @return			ikarus\system\database\adapter\IDatabaseAdapter
	 */
	public function getDefaultAdapter() {
		if (Ikarus::getConfiguration()->get('application.instance.primaryApplicationID') !== null and isset($this->applicationConnections[Ikarus::getConfiguration()->get('application.instance.primaryApplicationID')])) return $this->applicationConnections[Ikarus::getConfiguration()->get('application.instance.primaryApplicationID')];
		return $this->defaultAdapter;
	}

	/**
	 * Loads an adapter
	 * @param			string		$adapterName
	 * @throws			SystemException
	 * @return			void
	 */
	public function loadAdapter($adapterName) {
		// we don't need to reload adapters wich are already loaded
		if ($this->adapterIsLoaded($adapterName)) return;

		// generate class name
		$className = $adapterName.static::ADAPTER_SUFFIX;

		// create class path
		$classPath = IKARUS_DIR.static::ADAPTER_DIR.$className.'.class.php';

		// prepend namespace
		$className = static::ADAPTER_NAMESPACE.'\\'.$className;

		// try to locate class
		if (!file_exists($classPath)) throw new SystemException("Cannot load adapter %s: The needed class file (%s) does not exist", $adapterName, static::ADAPTER_DIR.$className.'.class.php');

		// try to load class
		if (!class_exists($className)) throw new SystemException("Cannot load adapter %s: The needed class (%s) was not defined in adapter file", $adapterName, $className);

		// check for support
		if (!call_user_func(array($className, 'isSupported'))) throw new SystemException("Cannot load adapter %s: The adapter is not supported by PHP installation", $adapterName);

		// add to array
		$this->loadedAdapters[] = $adapterName;
	}

	/**
	 * Sets the given handle as default adapter handle
	 * @param			ikarus\system\database\adapter\IDatabaseAdapter			$handle
	 * @return			void
	 */
	public function setDefaultAdapter(adapter\IDatabaseAdapter $handle, $applicationID = null) {
		// save application information
		if ($applicationID !== null) {
			$this->applicationConnections[$applicationID] = $handle;
			return;
		}

		// save data
		$this->defaultAdapter = $handle;
	}

	/**
	 * Starts the default database connection
	 * @throws		SystemException
	 * @return		void
	 */
	public function startDefaultAdapter() {
		// check for existing default adapter
		if ($this->defaultAdapter !== null) throw new SystemException("The default connection was already set");

		// validate configuration file
		if (!file_exists(IKARUS_DIR.static::DATABASE_CONFIGURATION_FILE)) throw new SystemException("Database configuration file '%s' does not exist", static::DATABASE_CONFIGURATION_FILE);
		if (!is_readable(IKARUS_DIR.static::DATABASE_CONFIGURATION_FILE)) throw new SystemException("Database configuration file '%s' is not readable", static::DATABASE_CONFIGURATION_FILE);

		// include configuration
		require_once(IKARUS_DIR.static::DATABASE_CONFIGURATION_FILE);

		// validate configuration content
		foreach($this->neededConfigurationVariables as $variable) if (!in_array($variable, array_keys(get_defined_vars()))) throw new SystemException("Needed variable '%s' was not found in database configuration file '%s'", $variable, static::DATABASE_CONFIGURATION_FILE);

		// load adapter
		$this->loadAdapter($adapterName);

		// create connection
		$connectionHandle = $this->createConnection($adapterName, $hostname, $port, $user, $password, $databaseParameters);

		// set charset
		$connectionHandle->setCharset($charset);

		// select correct database
		$connectionHandle->selectDatabase($databaseName);

		// set as default
		$this->setDefaultAdapter($connectionHandle);
	}

	/**
	 * Closes all active connections
	 * @return			void
	 */
	public function shutdown() {
		foreach($this->connections as $handle) {
			$handle->shutdown();
		}
	}
}
?>