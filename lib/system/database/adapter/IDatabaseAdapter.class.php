<?php
namespace ikarus\system\database\adapter;

/**
 * Defines needed methods for database adapters
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IDatabaseAdapter {
	
	/**
	 * Creates a new instance of type IDatabaseAdapter
	 * @param			string			$hostname
	 * @param			integer			$port
	 * @param			string			$user
	 * @param			string			$password
	 * @param			string			$databaseParameters
	 */
	public function __construct($hostname, $port, $user, $password, $databaseParameters);
	
	/**
	 * Escapes a string for use in queries
	 * @param			string			$string
	 * @return			string
	 */
	public function escapeString($string);
	
	/**
	 * Returns the client library version (if available)
	 * @return			string
	 */
	public function getClientVersion();
	
	/**
	 * Returns an instance of type IDatabaseEditor
	 * @return			ikarus\system\database\IDatabaseEditor
	 */
	public function getDatabaseEditor();
	
	/**
	 * Returns the name of the selected database (if any)
	 * @return			string
	 */
	public function getDatabaseName();
	
	/**
	 * Returns the last error message (if any)
	 * @return			string
	 */
	public function getErrorDescription();
	
	/**
	 * Returns additional information for DatabaseExceptions
	 * @return			array
	 */
	public function getErrorInformation();
	
	/**
	 * Returns the last error number (if any)
	 * @return			integer
	 */
	public function getErrorNumber();
	
	/**
	 * Returns the ID from the last insertion
	 * @param			string			$table
	 * @param			string			$field
	 * @return			integer
	 */
	public function getInsertID($table = null, $field = null);
	
	/**
	 * Returns the last executed query
	 * @return			string
	 */
	public function getLastQuery();
	
	/**
	 * Returns the result of last executed query
	 * @return			mixed
	 */
	public function getLastResult();
	
	/**
	 * Returns the value of specified parameter
	 * @param			string			$parameter
	 * @return			mixed
	 */
	public function getParameter($parameter);
	
	/**
	 * Returns the amount of sent queries in this application instance
	 * @return			integer
	 */
	public function getQueryCount();
	
	/**
	 * Returnes a list of valid quote delimiters (Such as ' or ")
	 * @return			array
	 */
	public function getQuoteDelimiter();
	
	/**
	 * Returns the username of current connection
	 * @return			string
	 */
	public function getUser();
	
	/**
	 * Returns the database server version
	 * @return			string
	 */
	public function getVersion();
	
	/**
	 * Manages the limit and offset parameter of sql queries
	 * @param			string			$query
	 * @param			integer			$limit
	 * @param			integer			$offset
	 * @return			string
	 */
	public function handleLimitParameter($query, $limit = 0, $offset = 0);
	
	/**
	 * Returns true if this type of database is supported
	 * @return			boolean
	 */
	public static function isSupported();
	
	/**
	 * Creates a prepared statement
	 * @param			string			$statement
	 * @param			integer			$limit
	 * @param			integer			$offset
	 * @return			ikarus\system\database\IPreparedStatement
	 */
	public function prepareStatement($statement, $limit = 0, $offset = 0);
	
	/**
	 * Quotes a string
	 * @param			string			$string
	 * @return			string
	 */
	public function quote($string);
	
	/**
	 * Selects the specified database for use
	 * @param			string			$databaseName
	 * @return			void
	 */
	public function selectDatabase($databaseName);
	
	/**
	 * Sends a query to database
	 * @param			string			$sql
	 * @return			mixed
	 */
	public function sendQuery($sql);
	
	/**
	 * Sets the charset for current connection
	 * @param			string			$charset
	 * @return			void
	 */
	public function setCharset($charset);
	
	/**
	 * Sets the given parameter to specified value
	 * @param			mixed			$parameter
	 * @param			mixed			$value
	 * @return			void
	 */
	public function setParameter($parameter, $value = null);
}
?>