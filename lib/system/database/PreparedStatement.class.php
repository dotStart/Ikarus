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
namespace ikarus\system\database;

use ikarus\system\exception\SystemException;

/**
 * Implements a default prepared statement
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class PreparedStatement implements IPreparedStatement {

	/**
	 * Contains the pattern used to get variables to fill
	 * @var                        string
	 */
	const STATEMENT_VARIABLE_PATTERN = '~(\?|\:[A-Z0-9_]+)~i';

	/**
	 * Contains the pattern used to detect named variables
	 * @var                        string
	 */
	const STATEMENT_VARIABLE_PATTERN_NAMED = '~(\:[A-Z0-9_]+)~i';

	/**
	 * Contains the adapter used for this statement
	 * @var                        ikarus\system\database\adapter\IDatabaseAdapter
	 */
	protected $adapter = null;

	/**
	 * Contains all bound variables with their positions
	 * @var                        array
	 */
	protected $boundVariables = array();

	/**
	 * Contains true if the initiator of this statement requested a DatabaseResultList
	 * @var                        boolean
	 */
	protected $forceList = false;

	/**
	 * Contains the statement (in SQL syntax)
	 * @var                        string
	 */
	protected $statement = '';

	/**
	 * Contains a splittet version of statement
	 * @var                        array<string>
	 */
	protected $statementSplit = array();

	/**
	 * Contains the maximum count of bound variables
	 * @var                        integer
	 */
	protected $variableCount = 0;

	/**
	 * Contains the current position (Used if no position was specified)
	 * @var                        integer
	 */
	protected $variablePosition = 0;

	/**
	 * Contains a sorted list of variables
	 * @var                        array<string>
	 */
	protected $variables = array();

	/**
	 * @see ikarus\system\database.IPreparedStatement::__construct()
	 */
	public function __construct (adapter\IDatabaseAdapter $adapter, $statement) {
		// save arguments
		$this->adapter = $adapter;
		$this->statement = $statement;

		// process statement
		$this->processStatement ();
	}

	/**
	 * @see ikarus\system\database.IPreparedStatement::bind()
	 */
	public function bind ($value, $position = null) {
		// get current position if needed
		if ($position === null) $position = $this->variablePosition++; // increase count after assignment

		// validate position
		if ($position >= $this->variableCount) throw new SystemException("Invalid variable position %u: The position is higher than the available amount of variables", $position);

		// save information
		$this->boundVariables[$position] = $value;
	}

	/**
	 * @see ikarus\system\database.IPreparedStatement::bindNamedParameter()
	 */
	public function bindNamedParameter ($name, $value) {
		// validate name
		if (!isset($this->namedVariables[$name])) throw new SystemException("Invalid variable name '%s': The name does not exist in statement");

		// get position
		return $this->bind ($value, $this->namedVariables[$name]);
	}

	/**
	 * Escapes the given value
	 * @param                        mixed $value
	 * @return                        string
	 */
	protected function escapeVariable ($value) {
		switch (gettype ($value)) {
			case 'boolean':
				return ($value ? 1 : 0);
				break;
			case 'float':
			case 'double': // this will never appear ...
			case 'integer':
				return $value;
				break;
			case 'NULL':
				return 'NULL';
				break;
			case 'array':
			case 'object':
				// convert object to string
				if (is_object ($value) and method_exists ($value, '__toString()')) return $this->adapter->quote ($value->__toString ());

				// serialize object
				return $this->adapter->quote (serialize ($value));
				break;
			case 'string':
			default:
				// we'll handle unknown data types in the same way as strings
				return $this->adapter->quote ($value);
				break;
		}
	}

	/**
	 * @see ikarus\system\database.IPreparedStatement::execute()
	 */
	public function execute () {
		$this->adapter->execute ($this->__toString ());
	}

	/**
	 * @see ikarus\system\database.IPreparedStatement::fetch()
	 */
	public function fetch () {
		return $this->adapter->sendQuery ($this->__toString (), false);
	}

	/**
	 * @see ikarus\system\database.IPreparedStatement::fetchList()
	 */
	public function fetchList () {
		// send query
		return $this->adapter->sendQuery ($this->__toString (), true);
	}

	/**
	 * Parses the statement and prepares for binding values
	 * @return                        void
	 */
	protected function processStatement () {
		// split statement
		$this->statementSplit = preg_split (static::STATEMENT_VARIABLE_PATTERN, $this->statement, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		// get named variables
		foreach ($this->statementSplit as $key => $element) {
			if (preg_match (static::STATEMENT_VARIABLE_PATTERN_NAMED, $element)) $this->namedVariables[$element] = $this->variableCount;
			if (preg_match (static::STATEMENT_VARIABLE_PATTERN, $element)) {
				// remove trailing quotes
				if (isset($this->statementSplit[($key - 1)])) $this->removeTrailingQuotes (($key - 1));

				// remove leading quotes
				if (isset($this->statementSplit[($key + 1)])) $this->removeLeadingQuotes (($key + 1));

				// save variable
				$this->variables[] = $element;
				$this->variableCount++;
			}
		}
	}

	/**
	 * Removes leading quotes
	 * @param                        integer $position
	 * @return                        void
	 */
	protected function removeLeadingQuotes ($position) {
		// get quote characters
		$quotes = $this->adapter->getQuoteDelimiter ();

		// remove first character if needed
		if (in_array ($this->statementSplit[$position]{0}, $quotes)) $this->statementSplit[$position] = substr ($this->statementSplit[$position], 1);
	}

	/**
	 * Removes trailing quotes
	 * @param                        integer $position
	 * @return                        void
	 */
	protected function removeTrailingQuotes ($position) {
		// get quote characters
		$quotes = $this->adapter->getQuoteDelimiter ();

		// remove first character if needed
		if (in_array ($this->statementSplit[$position]{(strlen ($this->statementSplit[$position]) - 1)}, $quotes)) $this->statementSplit[$position] = substr ($this->statementSplit[$position], 0, (strlen ($this->statementSplit[$position]) - 1));
	}

	/**
	 * Returns the complete sql query as string
	 * @throws                        SystemException
	 * @return                        string
	 */
	public function __toString () {
		// validate bound variables
		if (count ($this->boundVariables) < count ($this->variables)) throw new SystemException("Cannot create statement: The amount of bound variables does not match the amount of declared variables");

		$sql = "";
		$currentVariablePosition = 0;

		foreach ($this->statementSplit as $element) {
			if (preg_match (static::STATEMENT_VARIABLE_PATTERN, $element)) $sql .= $this->escapeVariable ($this->boundVariables[$currentVariablePosition++]); else
				$sql .= $element;
		}

		return $sql;
	}
}

?>