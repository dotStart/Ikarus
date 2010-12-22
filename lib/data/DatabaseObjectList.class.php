<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Handles a list of database objects
 * Note: This is an iterator. You cann loop through it with foreach()
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class DatabaseObjectList implements Iterator {

	/**
	 * Contains all iterateable database objects
	 * @var array
	 */
	protected $objectList = array();

	/**
	 * Points to the current database object
	 * @var integer
	 */
	protected $objectPointer = 0;

	/**
	 * Creates a new instance of DatabaseObjectList
	 * @param	array<DatabaseObject>	$objectList
	 */
	public function __construct($objectList) {
		$this->handleObjects($objectList);
	}

	/**
	 * Handles all given objects
	 * @param	array	$objectList
	 */
	protected function handleObjects($objectList) {
		$this->objectList = $objectList;
	}

	/** ITERATOR METHODS **/

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->objectPointer--;
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->objectPointer;
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		if (isset($this->objectList[$this->objectPointer]))
			return $this->objectList[$this->objectPointer];

		throw new SystemException("Iterator pointer out of index");
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		$this->objectPointer++;
	}
}
?>