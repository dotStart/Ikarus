<?php
namespace ikarus\system\database\adapter;
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
			$this->connection = new PDO($this->databaseParameters['connectionType'].':host='.$this->hostname.';port='.$this->port, $this->user, $this->password);
		
			// set attributes
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
		} catch (PDOException $ex) {
			throw new DatabaseException($this, "Cannot connect to database: %s", $ex->getMessage());
		}
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
			$this->lastResult = $this->connection->query($sql);
			return $this->lastResult;
		} catch (PDOException $ex) {
			throw new DatabaseException($this, 'Error with query: %s', $sql);
		}
	}
}
?>