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
namespace ikarus\system\session;

/**
 * Defines needed methods for sessions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface ISession {

	/**
	 * Creates a new instance of type ISession
	 * @param			array			$data
	 */
	public function __construct($data);
	
	/**
	 * Checks whether a session variable is read only.
	 * @param			string			$variableName
	 * @return			boolean
	 * @api
	 */
	public function isReadOnly($variableName);

	/**
	 * Sets a session variable.
	 * @param			string			$variableName
	 * @param			mixed			$variableValue
	 * @return			void
	 * @api
	 */
	public function setVariable($variableName, $variableValue);
	
	/**
	 * Updates the database row of this session
	 * @return			void
	 * @api
	 */
	public function update();
}
?>