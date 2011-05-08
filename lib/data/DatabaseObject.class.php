<?php
namespace ikarus\data;
use ikarus\system\exception\SystemException;

/**
 * Represents a database row
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
abstract class DatabaseObject {

	/**
	 * Contains all variables from database
	 * @var array
	 */
	protected $data = null;

	/**
	 * Creates a new DatabaseObject instance
	 * @param	array	$row
	 */
	public function __construct($row) {
		$this->handleData($row);
	}

	/**
	 * Handles data from database
	 * @param	array	$data
	 */
	protected function handleData($data) {
		$this->data = $data;
	}

	/**
	 * Magic method to handle properties from database row
	 * @param	string	$variable
	 * @return mixed
	 * @throws SystemException
	 */
	public function __get($variable) {
		// debugging
		if (DEBUG) assert(isset($this->data[$variable]));
		
		// handle variables in data array
		if (isset($this->data[$variable])) return $this->data[$variable];
		
		// no variable found
		return null;
	}

	/**
	 * Magic method to handle methods such as getXYZ and isXYZ
	 * @param	string	$name
	 * @param	array	$arguments
	 * @return mixed
	 * @throws SystemException
	 */
	public function __call($name, $arguments) {
		// handle getXYZ methods
		if (substr($name, 0, 3) == 'get' ) {
			$variable = substr($name, 3);
			$variable{0} = StringUtil::toLowerCase($variable{0});
			if (isset($this->data[$variable])) return $this->data[$variable];
		}

		// handle isXYZ methods
		if (substr($name, 0, 2) == 'is') {
			if (isset($this->data[$name])) return $this->data[$name];
		}

		// handle undefined methods
		throw new SystemException("Method '%s' does not exist in class %s", $name, get_class($this));
	}
}
?>