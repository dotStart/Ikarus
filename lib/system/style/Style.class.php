<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a style row
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class Style extends DatabaseObject {

	/**
	 * Creates a new instance of type Style
	 * @param	integer	$styleID
	 * @param	array	$row
	 */
	public function __construct($styleID, $row = null) {
			if ($styleID !== null) {
				$sql = "SELECT
							*
						FROM
							ikarus".IKARUS_N."_style
						WHERE
							styleID = ".$styleID;
				$row = IKARUS::getDatabase()->getFirstRow($sql);	
			}
			
			parent::__construct($row);
		}
}
?>