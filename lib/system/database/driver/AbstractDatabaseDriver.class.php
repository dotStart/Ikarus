<?php

/**
 * Defines default methods for database drivers
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
abstract class AbstractDatabaseDriver implements DatabaseDriver {
	
	/**
	 * Contains the database that we should use
	 * @var string
	 */
	protected $database = '';
	
	/**
	 * Contains database server's hostname
	 * @var string
	 */
	protected $hostname = '';

	/**
	 * Contains the result of the last query
	 * @var	mixed
	 */
	protected $lastResult = null;

	/**
	 * Contains the query string of the last executed query
	 * @var	string
	 */
	protected $lastQuery = "";

	/**
	 * Contains the link ID (e.g. a mysql resource)
	 * @var	mixed
	 */
	protected $linkID = null;

	/**
	 * Contains the count of all sent queries
	 * @var	integer
	 */
	protected $queryCount = 0;
	
	/**
	 * Contains the username that should used to connect to database
	 * @var string
	 */
	protected $username = '';

	/**
	 * @see lib/system/database/driver/DatabaseDriver::__construct()
	 */
	public function __construct($hostname, $username, $password, $database) {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::countRows()
	 */
	public function countRows($queryID = null) {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'countRows');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::escapeString()
	 */
	public function escapeString($string) {
		// You SHOULD really override this method! Addslashes isn't a good escape algorithm
		return addslashes($string);
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::fetchArray()
	 */
	public function fetchArray($queryID = null) {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'fetchArray');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getAffectedRows()
	 */
	public function getAffectedRows() {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getAffectedRows');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getColumns()
	 */
	public function getColumns($tableName) {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getColumns');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getErrorNumber()
	 */
	public function getErrorNumber() {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getErrorNumber');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getErrorDesc()
	 */
	public function getErrorDesc() {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getErrorDesc');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getFirstRow()
	 */
	public function getFirstRow($query, $limit = 1, $offset = 0) {
		$limit = (preg_match('/LIMIT\s+\d/i', $query) ? 0 : $limit);

		$query = $this->handleLimitParameter($query, $limit, $offset);

		$result = $this->sendQuery($query);

		if (is_resource($result)) {
			$row = $this->fetchArray($result);

			if (is_array($row)) {
				return $row;
			}
		}
		return false;
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getIndices()
	 */
	public function getIndices($tableName, $namesOnly = false) {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getIndices');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getInsertID()
	 */
	public function getInsertID($table = '', $field = '') {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getInsertID');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getTableNames()
	 */
	public function getTableNames($database = '') {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getTableNames');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::getVersion()
	 */
	public function getVersion() {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'getVersion');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::handleLimitParameter()
	 */
	public function handleLimitParameter($query = '', $limit = 0, $offset = 0) {
		if ($limit != 0) {
			if ($offset > 0) $query .= ' LIMIT '.$offset.', '.$limit;
			else $query .= ' LIMIT '.$limit;
		}

		return $query;
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::isSupported()
	 */
	public static function isSupported() {
		// Please insert the correct check here! The driver should only start if it's supported
		return true;
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::sendQuery()
	 */
	public function sendQuery($query, $limit = 0, $offset = 0) {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'sendQuery');
	}

	/**
	 * @see lib/system/database/driver/DatabaseDriver::shutdown()
	 */
	public function shutdown() {
		throw new SystemException("Unfinished %s definition in class %s (Missing method '%s')", 'DatabaseDriver', get_class($this), 'shutdown');
	}
}
?>