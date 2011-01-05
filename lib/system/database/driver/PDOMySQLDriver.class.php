<?php

/**
 * Database driver for PDO MySQL connections
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class PDOMySQLDriver extends AbstractDatabaseDriver {
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::__construct()
	 */
	public function __construct($hostname, $username, $password, $database) {
		// call parent method
		parent::__construct($hostname, $username, $password, $database);
		
		// create connection
		try {
			// create new PDO object
			$this->linkID = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
			
			// start transaction
			$this->linkID->beginTransaction();
		} catch (PDOException $ex) {
			throw new DatabaseException("PDO Error: %s", $ex->getMessage());
		}
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::countRows()
	 */
	public function countRows($queryID = null) {
		// get correct queryID
		if (!$queryID === null) $queryID = $this->lastResult;
		
		return $queryID->rowCount;
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::escapeString()
	 */
	public function escapeString($string) {
		// TODO: Implement Statements for all queries
		return $this->linkID->quote($string);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::fetchArray()
	 */
	public function fetchArray($queryID = null) {
		// get correct queryID
		if ($queryID === null) $queryID = $this->lastResult;
		
		// return array
		return $queryID->fetchAll();
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getAffectedRows()
	 */
	public function getAffectedRows() {
		throw new SystemException("Getting affected rows isn't supported in PDO driver");
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getColumns()
	 */
	public function getColumns($tableName) {
		// create and get variable
		$columns = array();
		$result = $this->sendQuery("SHOW COLUMNS FROM `".$tableName."`");
		
		// reformat array
		while($row = $this->fetchArray($result)) {
			$columns[] = $row['Field'];
		}
		
		return $columns;
	}
	
	/**
	 * Returnes error information from PDO driver
	 * @param	integer	$index
	 */
	protected  function getErrorInformation($index = 0) {
		$info = $this->linkID->errorInfo();
		return $info[$index];
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getErrorNumber()
	 */
	public function getErrorNumber() {
		return $this->getErrorInformation(1);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getErrorDesc()
	 */
	public function getErrorDesc() {
		return $this->getErrorInformation(2);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getIndices()
	 */
	public function getIndices($tableName, $namesOnly = false) {
		// create and get variables
		$indices = array();
		$result = $this->sendQuery("SHOW INDEX FROM `".$tableName."`");
		
		while($row = $this->fetchArray($result)) {
			if ($namesOnly)
				$indices[] = $row['Key_name'];
			else
				$indices[] = array('name' => $row['Key_name'], 'nonUnique' => $row['Non_unique'], 'columnName' => $row['Column_name'], 'type' => $row['Index_type']);
		}
		
		return $indices;
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getInsertID()
	 */
	public function getInsertID($table = '', $field = '') {
		return $this->linkID->lastInsertId();
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getTableNames()
	 */
	public function getTableNames($database = '') {
		// set optional arguments
		if (empty($database)) $database = $this->database;
		
		// create and get variables
		$names = array();
		$result = $this->sendQuery("SHOW TABLES FROM `".$database."`");
		
		while($row = $this->fetchArray($result)) {
			$names[] = $row[0];
		}
		
		return $names;
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getVersion()
	 */
	public function getVersion() {
		// get version
		$result = $this->getFirstRow('SELECT VERSION() AS version');
		
		// validate
		if (isset($result['version'])) return $result['version'];
		
		// no version string found
		return parent::getVersion();
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::isSupported()
	 */
	public static function isSupported() {
		return (class_exists('PDO', false) and in_array('mysql', PDO::getAvailableDrivers()));
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::sendQuery()
	 */
	public function sendQuery($query, $limit = 0, $offset = 0) {
		// clear old statemant
		if ($this->lastResult !== null) $this->lastResult->closeCursor();
		
		// handle limit parameter
		$query = $this->handleLimitParameter($query, $limit, $offset);
		
		// send query
		$this->lastResult = $this->linkID->query($query);
		
		// validate
		if (!$this->lastResult) throw new DatabaseException($this, "Invalid Query: %s", $query);
		
		return $this->lastQuery;
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::shutdown()
	 */
	public function shutdown() {
		if ($this->linkID !== null) $this->linkID = null;
	}
}
?>