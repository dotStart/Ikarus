<?php
namespace ikarus\system\database;

/**
 * Defines needed methods for prepared statements
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
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
	 * @param			boolean							$forceList
	 */
	public function __construct(adapter\IDatabaseAdapter $adapter, $statement, $forceList = false);
	
	/**
	 * Binds a parameter
	 * @param			mixed			$value
	 * @param			integer			$position
	 * @return			void
	 */
	public function bind($value, $position = null);
	
	/**
	 * Binds a named parameter
	 * @param			string			$name
	 * @param			mixed			$value
	 * @return			void
	 */
	public function bindNamedParameter($name, $value);
	
	/**
	 * Executes the statement
	 * @return			mixed
	 */
	public function execute();
}
?>