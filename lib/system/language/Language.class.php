<?php
namespace ikarus\system\language;
use ikarus\system\IKARUS;
use ikarus\data\DatabaseObject;

/**
 * Represents a language row
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class Language extends DatabaseObject {

	/**
	 * Creates a new instance of type Language
	 * @param	integer	$languageID
	 * @param	array	$row
	 */
	public function __construct($languageID, $row = null) {
		if ($languageID !== null) {
			$sql = "SELECT
					*
				FROM
					ikarus".IKARUS_N."_language
				WHERE
					languageID = ".$languageID;
			$row = IKARUS::getDatabase()->getFirstRow($sql);
		}

		parent::__construct($row);
	}

	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		// set data
		if (!$this->data['languageID']) $this->data['languageID'] = 0;
	}
}
?>