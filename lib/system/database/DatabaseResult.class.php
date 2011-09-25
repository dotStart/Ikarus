<?php
namespace ikarus\system\database;
use ikarus\data\DatabaseObject;

/**
 * Represents a database result
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DatabaseResult extends DatabaseObject {
	
	/**
	 * @see ikarus\data.DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		// handle arrays
		if (is_array($data)) return parent::handleData($data);
		
		// handle objects
		foreach($data as $key => $value) {
			$this->data[$key] = $value;
		}
	}
}
?>