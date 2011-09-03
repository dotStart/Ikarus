<?php
namespace ikarus\system\database\adapter;
use ikarus\system\database\DatabaseResultList;
use ikarus\system\exception\DatabaseException;
use \PDO;
use \PDOException;

/**
 * Generic adapter for PDO extension
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */

class GenericPDODatabaseAdapter extends AbstractDatabaseAdapter {
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::$neededDatabaseParameters
	 */
	protected $neededDatabaseParameters = array(
		'connectionType'
	);
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::connect()
	 */
	public function connect() {
		try {
			// connect
			$this->connection = new PDO($this->databaseParameters['connectionType'].':host='.$this->hostname.';port='.$this->port, $this->user, $this->password, array(PDO::ATTR_PERSISTENT => true));
		
			// set attributes
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			$this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
		} catch (PDOException $ex) {
			throw new DatabaseException($this, "Cannot connect to database: %s", $ex->getMessage());
		}
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getClientVersion()
	 */
	public function getClientVersion() {
		try {
			if ($this->connection !== null) return $this->connection->getAttribute(PDO::ATTR_CLIENT_VERSION);
		} Catch (PDOException $ex) { }
		
		return 'unknown';
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getErrorDescription()
	 */
	public function getErrorDescription() {
		if ($this->connection !== null) {
			$errorInformation = $this->connection->errorInfo();
			if (isset($errorInformation[2])) return $errorInformation[2];
		}
		return parent::getErrorDescription();
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getErrorInformation()
	 */
	public function getErrorInformation() {
		$information = array();
		
		try {
			if (!$this->connection) return $information;
			
			$information['pdo driver'] = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
			$information['connection status'] = $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
			$information['server information'] = $this->connection->getAttribute(PDO::ATTR_SERVER_INFO);
		} Catch (PDOException $ex) { }
		
		return $information;
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getErrorNumber()
	 */
	public function getErrorNumber() {
		if ($this->connection !== null) return $this->connection->errorCode();
		return parent::getErrorNumber();
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getInsertID()
	 */
	public function getInsertID($table = null, $field = null) {
		try {
			return $this->connection->getLastInsertId();
		} catch (PDOException $ex) {
			throw new DatabaseException($this, "Cannot fetch last insert ID");
		}
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getResultObject()
	 */
	protected function getResultObject($result, $forceList) {
		if ($result->rowCount() <= 0) return (new DatabaseResultList(array()));
		return parent::getResultObject($result, $forceList);
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::getVersion()
	 */
	public function getVersion() {
		try {
			if ($this->connection !== null) return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
		} Catch (PDOException $ex) { }
		return 'unknown';
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::isSupported()
	 */
	public static function isSupported() {
		return (extension_loaded('PDO'));
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::quote()
	 */
	public function quote($string) {
		return $this->connection->quote($string);
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::sendQuery()
	 */
	public function sendQuery($sql) {
		try {
			// save query
			$this->lastQuery = $sql;
			
			// get results
			$result = $this->connection->query($sql);
			
			// update query count
			$this->queryCount++;
			
			// return result object (if any)
			return $this->lastResult = $this->getResultObject($result);
		} catch (PDOException $ex) {
			$e = new DatabaseException($this, 'An error occoured while executing a database query');
			$e->setErrorQuery($sql);
			throw $e;
		}
	}
	
	/**
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::shutdown()
	 */
	public function shutdown() {
		$this->connection = null;
	}
}
?>