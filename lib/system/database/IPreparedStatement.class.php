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
namespace ikarus\system\database;

/**
 * Defines needed methods for prepared statements
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IPreparedStatement {
	
	/**
	 * Creates a new instance of type IPreparedStatement
	 * @param			ikarus\system\database\adapter\IDatabaseAdapter		$adapter
	 * @param			string							$statement
	 * @internal			This method will be called by it's parent adapter or manager.
	 */
	public function __construct(adapter\IDatabaseAdapter $adapter, $statement);
	
	/**
	 * Binds a parameter
	 * @param			mixed			$value
	 * @param			integer			$position
	 * @return			void
	 * @api
	 */
	public function bind($value, $position = null);
	
	/**
	 * Binds a named parameter
	 * @param			string			$name
	 * @param			mixed			$value
	 * @return			void
	 * @api
	 */
	public function bindNamedParameter($name, $value);
	
	/**
	 * Executes the statement
	 * @return			void
	 * @api
	 */
	public function execute();
	
	/**
	 * Fetches one or more items from database
	 * @return			mixed
	 * @api
	 */
	public function fetch();
	
	/**
	 * Fetches a list of items from database
	 * @return			ikarus\system\database\DatabaseResultList
	 * @api
	 */
	public function fetchList();
}
?>