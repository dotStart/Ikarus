<?php

/**
 * Defines needed default methods for database drivers
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface DatabaseDriver {

	/**
	 * Creates a new instance of DatabaseDriver
	 * @param	string	$hostname
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$database
	 */
	public function __construct($hostname, $username, $password, $database);

	/**
	 * Returnes a count of rows
	 * @param	integer	$queryID
	 * @return integer
	 */
	public function countRows($queryID = null);

	/**
	 * Escapes a string for use in database syntax
	 * Note: You should really use this to query database in a secure way!
	 * @param	string	$string
	 * @return string
	 */
	public function escapeString($string);

	/**
	 * Gets a row from database query result
	 * @param	integer	$queryID
	 * @return array
	 */
	public function fetchArray($queryID = null);

	/**
	 * Returnes the count of affected rows of last statement
	 * Note: This is only for statements such as INSERT, UPDATE, DELETE, ...
	 * @return integer
	 */
	public function getAffectedRows();

	/**
	 * Returnes all columns of a table
	 * @param	string	$tableName
	 * @return array
	 */
	public function getColumns($tableName);
	
	/**
	 * Returnes the name of this database driver
	 * @return string
	 */
	public function getDatabaseType();

	/**
	 * Returnes the error number of last error
	 * @return integer
	 */
	public function getErrorNumber();

	/**
	 * Returnes the last error message
	 */
	public function getErrorDesc();

	/**
	 * Returnes the first row
	 * @param	string	$query
	 * @param	integer	$limit
	 * @param	array	$offset
	 * @return array
	 */
	public function getFirstRow($query, $limit = 1, $offset = 0);

	/**
	 * Returnes all indices of a table
	 * @param	string	$tableName
	 * @param	boolean	$namesOnly
	 * @return array
	 */
	public function getIndices($tableName, $namesOnly = false);

	/**
	 * Returnes ID of last insert
	 * @param	string	$table
	 * @param	string	$field
	 * @return integer
	 */
	public function getInsertID($table = '', $field = '');

	/**
	 * Returnes names of all tables in database
	 * @param	string	$database
	 * @return array
	 */
	public function getTableNames($database = '');

	/**
	 * Returnes the version string of database server
	 * @return string
	 */
	public function getVersion();

	/**
	 * Handles the limit parameter in given query
	 * @param	string	$query
	 * @param	integer	$limit
	 * @param	integer	$offset
	 * @return string
	 */
	public function handleLimitParameter($query = '', $limit = 0, $offset = 0);

	/**
	 * Returnes true if the database driver is supported else false
	 * @return boolean
	 */
	public static function isSupported();

	/**
	 * Sends a query to database server
	 * @param	string	$query
	 * @param	integer	$limit
	 * @param	integer	$offset
	 * @return integer
	 */
	public function sendQuery($query, $limit = 0, $offset = 0);

	/**
	 * Shuts down the database driver
	 */
	public function shutdown();
}
?>