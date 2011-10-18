<?php
namespace ikarus\data;
use \Countable;
use \Iterator;
use ikarus\system\exception\SystemException;

/**
 * Handles a list of database objects
 * Note: This is an iterator. You cann loop through it with foreach()
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DatabaseObjectList implements Iterator, Countable {

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
	 * Returnes the count of all objects
	 * @return integer
	 */
	public function countObjects() {
		return count($this->objectList);
	}

	/**
	 * Converts the iterator to array
	 * @return array
	 */
	public function __toArray() {
		return $this->objectList;
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
	 * @see Countable::count()
	 */
	public function count() {
		return count($this->objectList);
	}
	
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->objectPointer = 0;
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
	
	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		return (isset($this->objectList[$this->objectPointer]));
	}
}
?>