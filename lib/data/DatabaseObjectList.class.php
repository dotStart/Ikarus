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
namespace ikarus\data;

use Countable;
use ikarus\system\event\data\DatabaseObjectListEventArguments;
use ikarus\system\event\data\HandleObjectsEvent;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use Iterator;

/**
 * Handles a list of database objects
 * Note: This is an iterator. You cann loop through it with foreach()
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class DatabaseObjectList implements Iterator, Countable {

	/**
	 * Contains all iterateable database objects
	 * @var                        array
	 */
	protected $objectList = array();

	/**
	 * Points to the current database object
	 * @var                        integer
	 */
	protected $objectPointer = 0;

	/**
	 * Creates a new instance of DatabaseObjectList
	 * @param                        array <DatabaseObject>                        $objectList
	 * @api
	 */
	public function __construct ($objectList) {
		$this->handleObjects ($objectList);
	}

	/**
	 * Converts the iterator to array
	 * @return                        array
	 * @api
	 */
	public function __toArray () {
		return $this->objectList;
	}

	/**
	 * Handles all given objects
	 * @param                        array $objectList
	 * @return                        void
	 * @api
	 */
	protected function handleObjects ($objectList) {
		// save data
		$this->objectList = $objectList;

		// fire event
		Ikarus::getEventManager ()->fire (new HandleObjectsEvent(new DatabaseObjectListEventArguments($this)));
	}

	/** ITERATOR METHODS **/

	/**
	 * @see Countable::count()
	 * @api
	 */
	public function count () {
		return count ($this->objectList);
	}

	/**
	 * @see Iterator::rewind()
	 * @api
	 */
	public function rewind () {
		$this->objectPointer = 0;
	}

	/**
	 * @see Iterator::key()
	 * @api
	 */
	public function key () {
		return $this->objectPointer;
	}

	/**
	 * @see Iterator::current()
	 * @api
	 */
	public function current () {
		if (isset($this->objectList[$this->objectPointer])) return $this->objectList[$this->objectPointer];

		throw new SystemException("Iterator pointer out of index");
	}

	/**
	 * @see Iterator::next()
	 * @api
	 */
	public function next () {
		$this->objectPointer++;
	}

	/**
	 * @see Iterator::valid()
	 * @api
	 */
	public function valid () {
		return (isset($this->objectList[$this->objectPointer]));
	}
}

?>