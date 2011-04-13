<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a user row
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
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