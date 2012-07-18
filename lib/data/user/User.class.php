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
namespace ikarus\data\user;
use ikarus\data\DatabaseObject;
use ikarus\system\IKARUS;

/**
 * Represents a user row
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class User extends DatabaseObject {

	/**
	 * Reads a row from database
	 * @param	integer	$userID
	 * @param	array	$row
	 */
	public function __construct($userID, $row = null) {
		if ($userID !== null) {
			$sql = "SELECT
					*
				FROM
					ikarus".IKARUS_N."_user
				WHERE
					userID = ".$userID;
			$row = IKARUS::getDatabase()->getFirstRow($sql);
		}

		parent::__construct($row);
	}

	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		// set userID to zero value
		if (!isset($this->data['userID'])) $this->data['userID'] = 0;
	}
}
?>