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

/**
 * Defines needed methods for database adapters
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IDatabaseAdapter {
	
	/**
	 * Creates a new instance of type IDatabaseAdapter.
	 * @param			string			$hostname
	 * @param			integer			$port
	 * @param			string			$user
	 * @param			string			$password
	 * @param			string			$databaseParameters
	 * @internal			This method will be called thru it's parent manager.
	 */
	public function __construct($hostname, $port, $user, $password, $databaseParameters);
	
	/**
	 * Escapes a string for use in queries.
	 * @param			string			$string
	 * @return			string
	 * @api
	 */
	public function escapeString($string);
	
	/**
	 * Executes a query.
	 * @param			string			$query
	 * @return			void
	 * @api
	 */
	public function execute($query);
	
	/**
	 * Returns the client library version (if available).
	 * @return			string
	 * @api
	 */
	public function getClientVersion();
	
	/**
	 * Returns an instance of type IDatabaseEditor.
	 * @return			ikarus\system\database\IDatabaseEditor
	 * @api
	 */
	public function getDatabaseEditor();
	
	/**
	 * Returns the name of the selected database (if any).
	 * @return			string
	 * @api
	 */
	public function getDatabaseName();
	
	/**
	 * Returns the last error message (if any).
	 * @return			string
	 * @api
	 */
	public function getErrorDescription();
	
	/**
	 * Returns additional information for DatabaseExceptions.
	 * @return			array
	 * @api
	 */
	public function getErrorInformation();
	
	/**
	 * Returns the last error number (if any).
	 * @return			integer
	 * @api
	 */
	public function getErrorNumber();
	
	/**
	 * Returns the ID from the last insertion.
	 * @param			string			$table
	 * @param			string			$field
	 * @return			integer
	 * @api
	 */
	public function getInsertID($table = null, $field = null);
	
	/**
	 * Returns the last executed query.
	 * @return			string
	 * @api
	 */
	public function getLastQuery();
	
	/**
	 * Returns the result of last executed query.
	 * @return			mixed
	 * @api
	 */
	public function getLastResult();
	
	/**
	 * Returns the value of specified parameter.
	 * @param			string			$parameter
	 * @return			mixed
	 * @api
	 */
	public function getParameter($parameter);
	
	/**
	 * Returns the amount of sent queries in this application instance.
	 * @return			integer
	 * @api
	 */
	public function getQueryCount();
	
	/**
	 * Returnes a list of valid quote delimiters (Such as ' or ").
	 * @return			array
	 * @api
	 */
	public function getQuoteDelimiter();
	
	/**
	 * Returns the username of current connection.
	 * @return			string
	 * @api
	 */
	public function getUser();
	
	/**
	 * Returns the database server version.
	 * @return			string
	 * @api
	 */
	public function getVersion();
	
	/**
	 * Manages the limit and offset parameter of sql queries.
	 * @param			string			$query
	 * @param			integer			$limit
	 * @param			integer			$offset
	 * @return			string
	 * @internal			This method is used internally by this adapter.
	 */
	public function handleLimitParameter($query, $limit = 0, $offset = 0);
	
	/**
	 * Returns true if this type of database is supported.
	 * @return			boolean
	 * @api
	 */
	public static function isSupported();
	
	/**
	 * Parses a query and replaces application abbreviations.
	 * @param			string			$query
	 * @return			string
	 * @api
	 */
	public function parseQuery($query);
	
	/**
	 * Creates a prepared statement.
	 * @param			string			$statement
	 * @param			integer			$limit
	 * @param			integer			$offset
	 * @param			boolean			$forceList
	 * @return			ikarus\system\database\IPreparedStatement
	 * @api
	 */
	public function prepareStatement($statement, $limit = 0, $offset = 0, $forceList = false);
	
	/**
	 * Quotes a string.
	 * @param			string			$string
	 * @return			string
	 * @api
	 */
	public function quote($string);
	
	/**
	 * Selects the specified database for use.
	 * @param			string			$databaseName
	 * @return			void
	 * @api
	 */
	public function selectDatabase($databaseName);
	
	/**
	 * Sends a query to database.
	 * @param			string			$query
	 * @param			boolean			$forceList
	 * @return			mixed
	 * @api
	 */
	public function sendQuery($query, $forceList = false);
	
	/**
	 * Sets the charset for current connection.
	 * @param			string			$charset
	 * @return			void
	 * @api
	 */
	public function setCharset($charset);
	
	/**
	 * Sets the given parameter to specified value.
	 * @param			mixed			$parameter
	 * @param			mixed			$value
	 * @return			void
	 * @api
	 */
	public function setParameter($parameter, $value = null);
}
?>