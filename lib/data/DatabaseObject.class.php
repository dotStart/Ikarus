<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\data;
use ikarus\system\database\QueryEditor;
use ikarus\system\event\data\DatabaseObjectEventArguments;
use ikarus\system\event\data\GetByIdentifierEvent;
use ikarus\system\event\data\HandleDataEvent;
use ikarus\system\event\data\MagicGetEvent;
use ikarus\system\event\IdentifierEventArguments;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\exception\SystemException;

/**
 * Represents a database row
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class DatabaseObject {

	/**
	 * Contains all variables from database
	 * @var array
	 */
	protected $data = null;

	/**
	 * Contains the field name for row identifier (or primary key)
	 * @var			string
	 */
	protected static $identifierField = null;

	/**
	 * Contains the name of the table where all data of this database object is stored
	 * @var			string
	 */
	protected static $tableName = null;

	/**
	 * Creates a new DatabaseObject instance
	 * @param	array	$row
	 */
	public function __construct($row) {
		$this->handleData($row);
	}

	/**
	 * Returns an object by identifier (if configured)
	 * @param			mixed			$identifier
	 * @throws			SystemException
	 * @returns			ikarus\data\DatabaseObject
	 */
	public static function getByIdentifier($identifier) {
		// fire event
		$event = new GetByIdentifierEvent(new IdentifierEventArguments($identifier));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new StrictStandardException("Missing replacement for database object");
		elseif ($event->isCancelled())
			return $event->getReplacement();

		// check for configuration
		if (!static::$tableName or static::$identifierField) throw new SystemException('The database object %s is not configured for method %s', __CLASS__, __METHOD__);

		// get data via editor
		$editor = new QueryEditor();
		$editor->from(array(static::$tableName => 'databaseObjectTable'));
		$editor->where(static::$identifierField.' = ?');
		$stmt = $editor->prepare();
		$stmt->bind($identifier);
		$result = $stmt->fetch();

		// no rows found?
		if (!$result) return null;

		// create object
		return (new static($result->__toArray()));
	}

	/**
	 * Handles data from database
	 * @param	array	$data
	 */
	protected function handleData($data) {
		// save data
		$this->data = $data;

		// fire event
		Ikarus::getEventManager()->fire(new HandleDataEvent(new DatabaseObjectEventArguments($this)));
	}

	/**
	 * Checks whether the given variable exists in this database object
	 * @param			string			$variable
	 * @return			boolean
	 */
	public function __isset($variable) {
		return array_key_exists($variable, $this->data);
	}

	/**
	 * Unsets the given variable (Support for unset($databaseObject->variable))
	 * @param			string			$variable
	 * @return			void
	 */
	public function __unset($variable) {
		if (!$this->_isset($variable)) return;
		unset($this->data[$variable]);
	}

	/**
	 * Magic method to handle properties from database row
	 * @param			string			$variable
	 * @return			mixed
	 * @throws			SystemException
	 */
	public function __get($variable) {
		// fire event
		$event = new MagicGetEvent(new IdentifierEventArguments($variable));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new StrictStandardException('Missing replacement for variable "%s"', $variable);
		elseif ($event->isCancelled())
			return $event->getReplacement();

		// strict standard
		if (!$this->__isset($variable)) throw new StrictStandardException("The variable '%s' is not defined in DatabaseObject %s", $variable, get_class($this));

		// handle variables in data array
		if (array_key_exists($variable, $this->data)) return $this->data[$variable];

		// no variable found
		return null;
	}

	/**
	 * Magic method to handle methods such as getXYZ and isXYZ
	 * @param			string			$name
	 * @param			array			$arguments
	 * @return			mixed
	 * @throws			SystemException
	 */
	public function __call($name, $arguments) {
		// handle getXYZ methods
		if (substr($name, 0, 3) == 'get' ) {
			$variable = substr($name, 3);
			$variable{0} = StringUtil::toLowerCase($variable{0});
			if (isset($this->__isset($variable))) return $this->__get($variable);
		}

		// handle isXYZ methods
		if (substr($name, 0, 2) == 'is' and isset($this->__isset($name))) return $this->__get($name);

		// handle undefined methods
		throw new SystemException("Method '%s' does not exist in class %s", $name, get_class($this));
	}
}
?>