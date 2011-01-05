<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/database/driver/AbstractDatabaseDriver.class.php');

/**
 * Database driver for MySQL database system
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class MySQLDatabaseDriver extends AbstractDatabaseDriver {
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::__construct()
	 */
	public function __construct($hostname, $username, $password, $database) {
		// call parent method
		parent::__construct($hostname, $username, $password, $database);
		
		// create connection
		$this->linkID = mysql_connect($hostname, $username, $password, true);
		
		// check for errors
		if (!$this->linkID) throw new DatabaseException($this, "Cannot connect to database server '%s' with database driver '%s'", $hostname, get_class($this));
		
		// set database
		$this->selectDatabase($database);
	}
	
	/**
	 * Selects the correct database (Sends a USE <database> to server)
	 * @param	string	$database
	 */
	protected function selectDatabase($database) {
		if (!@mysql_select_db($database, $this->linkID))
			throw new DatabaseException($this, "Cannot select database '%s'", $database);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::countRows()
	 */
	public function countRows($queryID = null) {
		return mysql_num_rows(($queryID !== null ? $queryID : $this->lastResult));
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::escapeString()
	 */
	public function escapeString($string) {
		return mysql_real_escape_string($string, $this->linkID);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::fetchArray()
	 */
	public function fetchArray($queryID = null) {
		return mysql_fetch_array(($queryID !== null ? $queryID : $this->lastResult));
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getAffectedRows()
	 */
	public function getAffectedRows() {
		return mysql_affected_rows($this->linkID);
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
	 * @see lib/system/database/driver/DatabaseDriver::getErrorNumber()
	 */
	public function getErrorNumber() {
		return mysql_errno($this->linkID);
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::getErrorDesc()
	 */
	public function getErrorDesc() {
		return mysql_error($this->linkID);
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
		return mysql_insert_id($this->linkID);
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
		return function_exists('mysql_connect');
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::sendQuery()
	 */
	public function sendQuery($query, $limit = 0, $offset = 0) {
		// handle limit parameter
		$query = $this->handleLimitParameter($query, $limit, $offset);
		
		// send query
		$this->lastResult = mysql_query($query, $this->linkID);
		
		// validate
		if (!$this->lastResult) throw new DatabaseException($this, "Invalid Query: %s", $query);
		
		return $this->lastResult;
	}
	
	/**
	 * @see lib/system/database/driver/DatabaseDriver::shutdown()
	 */
	public function shutdown() {
		if ($this->linkID !== false) mysql_close($this->linkID);
	}
}
?>