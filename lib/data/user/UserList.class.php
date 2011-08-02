<?php
namespace ikarus\data\user;
use ikarus\data\DatabaseObjectList;
use ikarus\system\IKARUS;

/**
 * Manages a list of users
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class UserList extends DatabaseObjectList {

	/**
	 * Contains the name of the class that should used in list
	 * @var string
	 */
	protected $listObject = 'UserProfile';

	/**
	 * Creates a new instance of UserList
	 * @param	integer	$limit
	 * @param	integer	$offset
	 * @param	string	$whereClouse
	 */
	public function __construct($limit = 0, $offset = 0, $whereClouse = null) {
		$objectList = array();
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_user
			".($whereClouse !== null ? "WHERE ".$whereClouse : "");
		$result = IKARUS::getDatabase()->sendQuery($sql, $limit, $offset);

		while($row = IKARUS::getDatabase()->fetchArray($result)) {
			$objectList[] = new $this->listObject(null, $row);
		}

		parent::__construct($objectList);
	}
}
?>