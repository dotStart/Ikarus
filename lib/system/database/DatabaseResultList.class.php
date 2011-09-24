<?php
namespace ikarus\system\database;
use ikarus\data\DatabaseObjectList;

/**
 * Represents a list of results
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DatabaseResultList extends DatabaseObjectList {
	
	/**
	 * @see ikarus\data.DatabaseObjectList::handleObjects()
	 */
	protected function handleObjects($objectList) {
		foreach($objectList as $object) {
			$this->objectList[] = ($object instanceof DatabaseResult ? $object : new DatabaseResult($object));
		}
	}
}
?>