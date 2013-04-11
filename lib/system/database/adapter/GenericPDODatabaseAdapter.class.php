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
namespace ikarus\system\database\adapter;
use ikarus\system\database\DatabaseResultList;
use ikarus\system\exception\database\DatabaseException;
use \PDO;
use \PDOException;

/**
 * Generic adapter for PDO extension
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
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
			// create dsn
			if ($this->databaseParameters['connectionType'] != 'sqlite')
				$dsn = $this->databaseParameters['connectionType'].':host='.$this->hostname.';port='.$this->port;
			else
				$dsn = 'sqlite:'.$this->hostname;
				
			// connect
			$this->connection = new PDO($dsn, $this->user, $this->password, array(PDO::ATTR_PERSISTENT => true));

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
	 * @see ikarus\system\database\adapter.AbstractDatabaseAdapter::execute()
	 */
	public function execute($query) {
		try {
			// parse query
			$this->parseQuery($query);

			// save query
			$this->lastQuery = $query;

			// get results
			$this->connection->query($query);

			// update query count
			$this->queryCount++;
		} catch (PDOException $ex) {
			$e = new DatabaseException($this, 'An error occurred while executing a database query');
			$e->setErrorQuery($query);
			throw $e;
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
	protected function getResultObject($result, $forceList = false) {
		$resultList = array();
		while($row = $result->fetch()) {
			$resultList[] = $row;
		}

		if ($result->rowCount() <= 0) return (new DatabaseResultList(array()));
		return parent::getResultObject($resultList, $forceList);
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
	public function sendQuery($query, $forceList = false) {
		try {
			// parse query
			$this->parseQuery($query);

			// save query
			$this->lastQuery = $query;

			// get results
			$result = $this->connection->query($query);

			// update query count
			$this->queryCount++;

			// return result object (if any)
			return $this->lastResult = $this->getResultObject($result, $forceList);
		} catch (PDOException $ex) {
			$e = new DatabaseException($this, 'An error occurred while executing a database query');
			$e->setErrorQuery($query);
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