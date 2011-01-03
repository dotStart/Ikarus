<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/database/driver/DatabaseDriver.class.php');
require_once(IKARUS_DIR.'lib/system/database/driver/AbstractDatabaseDriver.class.php');

/**
 * Manages database connections
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class DatabaseManager {

	/**
	 * Contains all available connections
	 * @var array<DatabaseDriver>
	 */
	protected $availableConnections = array();

	/**
	 * Contains the linkID of active driver
	 * @var	string
	 */
	protected $activeDriverID = '';

	/**
	 * Adds a connection to connection pool
	 * @param	string	$driver
	 * @param	string	$hostname
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$database
	 * @return string
	 * @throws SystemException
	 */
	public function addConnection($driver, $hostname, $username, $password, $database) {
		// try to find database driver
		if (!file_exists(IKARUS_DIR.'lib/system/database/driver/'.$driver.'.class.php')) throw new SystemException("Cannot load database driver '%s'", $driver);

		// include driver
		require_once(IKARUS_DIR.'lib/system/database/driver/'.$driver.'.class.php');

		// create new instance
		$linkID = $database.'.'.str_replace('DatabaseDriver', '', $driver);

		if (call_user_func(array($driver, 'isSupported'))) {
			$this->availableConnections[$linkID] = new $driver($hostname, $username, $password, $database);

			// set active
			$this->activeDriverID = $linkID;
		} else {
			throw new SystemException("Trying to load an unsupported database driver: %s", $driver);
		}

		// return linkID
		return $linkID;
	}

	/**
	 * Sets a driver as active
	 * @param	integer	$linkID
	 * @throws SystemException
	 */
	public function setActiveDriver($linkID) {
		// validate
		if (!isset($this->availableConnections[$linkID])) throw new SystemException("Unknown database link '%s'", $linkID);

		// set active
		$this->activeDriverID = $linkID;
	}

	/**
	 * Returnes the current active driver
	 * @return DatabaseDriver
	 */
	public function getActiveDriver() {
		return $this->availableConnections[$this->activeDriverID];
	}

	/**
	 * Returnes the type of the active driver
	 * @return string
	 */
	public function getActiveDriverType() {
		return get_class($this->getActiveDriver());
	}

	/**
	 * Shuts down all database connections
	 */
	public function shutdown() {
		foreach($this->availableConnections as $linkID => $connection) {
			$this->availableConnections[$linkID]->shutdown();
		}
	}

	/**
	 * Redirects method calls to active driver
	 * @param	string	$method
	 * @param	array	$arguments
	 * @return mixed
	 * @throws SystemException
	 */
	public function __call($method, $arguments) {
		if (method_exists($this->availableConnections[$this->activeDriverID], $method))
			return call_user_func_array($this->availableConnections[$this->activeDriverID], $arguments);

		throw new SystemException("Method '%s' does not exist in class %s", $method, get_class($this));
	}

	/**
	 * Redirects properties to active driver
	 * @param	string	$property
	 * @return mixed
	 * @throws SystemException
	 */
	public function __get($property) {
		if (property_exists($this->availableConnections[$this->activeDriverID], $property))
			return $this->availableConnections[$this->activeDriverID]->{$property};

		throw new SystemException("Property '%s' does not exist in class %s", $property, get_class($this));
	}
}
?>