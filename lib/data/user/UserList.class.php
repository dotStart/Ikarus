<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\data\user;

use ikarus\data\DatabaseObjectList;
use ikarus\system\IKARUS;

/**
 * Manages a list of users
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class UserList extends DatabaseObjectList {

	/**
	 * Contains the name of the class that should used in list
	 * @var string
	 */
	protected $listObject = 'UserProfile';

	/**
	 * Creates a new instance of UserList
	 * @param        integer $limit
	 * @param        integer $offset
	 * @param        string  $whereClouse
	 */
	public function __construct ($limit = 0, $offset = 0, $whereClouse = null) {
		$objectList = array ();
		$sql = "SELECT
				*
			FROM
				ikarus" . IKARUS_N . "_user
			" . ($whereClouse !== null ? "WHERE " . $whereClouse : "");
		$result = IKARUS::getDatabase ()->sendQuery ($sql, $limit, $offset);

		while ($row = IKARUS::getDatabase ()->fetchArray ($result)) {
			$objectList[] = new $this->listObject(null, $row);
		}

		parent::__construct ($objectList);
	}
}

?>