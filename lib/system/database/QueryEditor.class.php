<?php
namespace ikarus\system\database;
use ikarus\system\Ikarus;

/**
 * Provides a class for creating dynamic database queries
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 * @todo		This will curently only work for SELECT queries. We have to add more code for UPDATE and DELETE queries
 */
class QueryEditor {

	const COLUMNS			= 'columns';
	const DISTINCT			= 'distinct';
	const FROM			= 'from';
	const FOR_UPDATE		= 'forupdate';
	const GROUP			= 'group';
	const HAVING			= 'having';
	const LIMIT_COUNT		= 'limitcount';
	const LIMIT_OFFSET		= 'limitoffset';
	const ORDER			= 'order';
	const UNION			= 'union';
	const WHERE			= 'where';

	const CROSS_JOIN		= 'cross join';
	const FULL_JOIN			= 'full join';
	const INNER_JOIN		= 'inner join';
	const LEFT_JOIN			= 'left join';
	const NATURAL_JOIN		= 'natural join';
	const RIGHT_JOIN		= 'right join';
	
	const TYPE_AND			= 0;
	const TYPE_OR			= 1;

	const SQL_AND			= 'AND';
	const SQL_AS			= 'AS';
	const SQL_ASC			= 'ASC';
	const SQL_DESC			= 'DESC';
	const SQL_DISTINCT		= 'DISTINCT';
	const SQL_FROM			= 'FROM';
	const SQL_FOR_UPDATE		= 'FOR UPDATE';
	const SQL_GROUP_BY		= 'GROUP BY';
	const SQL_HAVING		= 'HAVING';
	const SQL_ON			= 'ON';
	const SQL_OR			= 'OR';
	const SQL_ORDER_BY		= 'ORDER BY';
	const SQL_SELECT		= 'SELECT';
	const SQL_UNION			= 'UNION';
	const SQL_UNION_ALL		= 'UNION ALL';
	const SQL_WHERE			= 'WHERE';
	const SQL_WILDCARD		= '*';

	/**
	 * Contains initial values for query parts array
	 * @var			array
	 */
	protected static $partsInit = array(
		self::DISTINCT	=> false,
		self::COLUMNS		=> array(),
		self::UNION		=> array(),
		self::FROM		=> array(),
		self::WHERE		=> array(),
		self::GROUP		=> array(),
		self::HAVING		=> array(),
		self::ORDER		=> array(),
		self::LIMIT_COUNT	=> null,
		self::LIMIT_OFFSET	=> null,
		self::FOR_UPDATE	=> false
	);

	/**
	 * Contains a list of valid join types
	 * @var			array
	 */
	protected static $validJoinTypes = array(
		self::INNER_JOIN,
		self::LEFT_JOIN,
		self::RIGHT_JOIN,
		self::FULL_JOIN,
		self::CROSS_JOIN,
		self::NATURAL_JOIN,
	);

	/**
	 * Contains a list of valid join types
	 * @var			array
	 */
	protected static $validUnionTypes = array(
		self::SQL_UNION,
		self::SQL_UNION_ALL
	);

	/**
	 * Contains all parts for the query
	 * @var			array
	 */
	protected $parts = array();

	/**
	 * Contains a list of columns wich should read
	 * @var			array
	 */
	protected $columns = array();

	/**
	 * Creates a new instance of type QueryEditor
	 */
	public function __construct() {
		$this->parts = static::$partsInit;
	}
	
	/**
	 * Specifies the columns used in the FROM clause.
	 * @param			mixed			$columns
	 * @param			string			$correlationName
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function columns($columns = '*', $correlationName = null) {
		// get values for optional arguments
		if ($correlationName === null and count($this->parts[static::FROM])) {
			$correlationNameKeys = array_keys($this->parts[static::FROM]);
			$correlationName = current($correlationNameKeys);
		}

		// validate input
		if (!array_key_exists($correlationName, $this->parts[static::FROM])) throw new SystemException("No table for FROM clouse specified");
		
		// execute
		$this->setTableColumns($correlationName, $columns);
		return $this;
	}

	/**
	 * Support for SELECT DISTINCT.
	 * @param			boolean			$flag
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function distinct($flag = true) {
		$this->parts[static::DISTINCT] = (bool) $flag;
		return $this;
	}

	/**
	 * Support for SELECT FROM ...
	 * @param			mixed			$table
	 * @param			mixed			$columns
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function from($table, $columns = '*') {
		return $this->join(static::FROM, $table, null, $columns);
	}
	
	/**
	 * Returns the corret column sql string
	 * @param			mixed			$column
	 * @param			string			$alias
	 */
	protected function getColumnString($column, $alias) {
		if (is_array($column))
			list($correlationName, $columnName) = $column;
		else {
			$columnName = $column;
			$correlationName = '';
		}
			
		return (empty($correlationName) ? '' : $correlationName.'.').$columnName.(empty($alias) ? '' : static::SQL_AS.' '.$alias);
	}
	
	/**
	 * Returnes the sql query for table names
	 * @param			string			$tableName
	 * @param			string			$correlationName
	 * @return			string
	 */
	protected function getTableString($tableName, $correlationName) {
		return $tableName.(empty($correlationName) ? '' : ' '.static::SQL_AS.' '.$correlationName);
	}
	
	/**
	 * Returns the correct type
	 * @param			integer			$type
	 * @throws			SystemException
	 * @return			string
	 */
	protected function getType($type = self::TYPE_AND) {
		switch($type) {
			case static::TYPE_AND:
				return static::SQL_AND;
				break;
			case static::TYPE_OR:
				return static::SQL_OR;
				break;
			default:
				throw new SystemException("Invalid having type '%s' passed to method ".__CLASS__."::having()");
				break;
		}
	}
	
	/**
	 * Generate a unique correlation name
	 * @param			mixed			$name
	 * @return			string
	 */
	protected function getUniqueCorrelationName($name) {
		if (is_array($name))
			$name = end($name);
		else {
			// Extract just the last name of a qualified table name
			$dot = strrpos($name, '.');
			$name = ($dot === false ? $name : substr($name, $dot + 1));
		}
		
		$correlationName = $name;
		
		for($i = 1; array_key_exists($correlationName, $this->parts[static::FROM]); $i++) {
			$correlationName = $name.'_'.$i;
		}
		
		return $correlationName;
	}

	/**
	 * Adds grouping to the query.
	 * @param			mixed			$columns
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function group($columns) {
		// unify $columns
		if (!is_array($columns)) $columns = array(0 => $columns);

		// execute
		foreach ($columns as $column) {
			$this->parts[static::GROUP][] = $column;
		}
		return $this;
	}

	/**
	 * Adds a HAVING condition to the query by AND.
	 * @param			string			$condition
	 * @param			int			$type
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function having($condition, $type = self::TYPE_AND) {
		if ($this->parts[static::HAVING])
			$this->parts[static::HAVING][] = $this->getType($type).' ('.$condition.')';
		else
			$this->parts[static::HAVING][] = '('.$condition.')';
		return $this;
	}
	
	/**
	 * Adds a JOIN clause to query
	 * @param			string			$type
	 * @param			string			$table
	 * @param			string			$condition
	 * @param			mixed			$columns
	 * @throws			SystemException
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function join($type, $table, $condition, $columns) {
		// validate type
		if (!in_array($type, static::$validJoinTypes) and $type != static::FROM) throw new SystemException("Invalid join type '%s' passed to method ".__CLASS__."::join()");
		
		// check for existing unions
		if (count($this->parts[static::UNION])) throw new SystemException("Invalid use of JOIN with UNION");

		if (empty($table))
			$correlationName = $tableName = '';
		else if (is_array($table)) {
			// The array format is array($correlationName => $tableName)
			foreach ($table as $_tableName => $_correlationName) {
				// We assume the key is the correlation name and value is the table name
				$tableName = $_tableName;
				$correlationName = $_correlationName;
				break;
			}
		} else if (preg_match('~^(.+)\s+AS\s+(.+)$~i', $table, $matches)) {
			$tableName = $matches[1];
			$correlationName = $matches[2];
		} else {
			$tableName = $table;
			$correlationName = $this->getUniqueCorrelationName($tableName);
		}

		if (!empty($correlationName)) {
			// validate name
			if (array_key_exists($correlationName, $this->parts[static::FROM])) throw new SystemException("Cannot redefine correlation name '%s'", $correlationName);

			$this->parts[static::FROM][$correlationName] = array(
				'joinType'		=> $type,
				'tableName'		=> $tableName,
				'joinCondition'		=> $condition
			);
		}
		
		// add to the columns from this joined table
		$this->setTableColumns($correlationName, $columns);
		return $this;
	}

	/**
	 * Sets a limit count and offset to the query.
	 * @param			int			$count
	 * @param			int			$offset
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function limit($count = null, $offset = null) {
		$this->parts[static::LIMIT_COUNT] = (int) $count;
		$this->parts[static::LIMIT_OFFSET] = (int) $offset;
		return $this;
	}
	
	/**
	 * Sets the limit and count by page number.
	 * @param			int			$page
	 * @param			int			$itemsPerPage
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function limitPage($page, $itemsPerPage = 20) {
		$this->parts[static::LIMIT_COUNT] = (int) $rowCount;
		$this->parts[static::LIMIT_OFFSET] = (int) ($rowCount * ($page - 1));
		return $this;
	}
	
	/**
	 * Adds a row order to the query.
	 * @param			mixed			$columns
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function order($columns) {
		// unify $columns
		if (!is_array($columns)) $columns = array(0 => $columns);

		// force 'ASC' or 'DESC' on each order column, default is ASC.
		foreach ($columns as $column) {
			// no value given ... skip
			if (empty($column)) continue;
			
			// set initial direction
			$direction = static::SQL_ASC;
			
			// split string
			// TODO: This will only detect simple queries. We have to add support for subqueries here
			if (preg_match('~(.*\W)('.static::SQL_ASC.'|'.static::SQL_DESC.')\b~si', $column, $matches)) {
				$column = trim($matches[1]);
				$direction = $matches[2];
			}
			
			// add
			$this->parts[static::ORDER][] = array($column, $direction);
		}

		return $this;
	}
	
	/**
	 * Creates a prepared statement for this query
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::prepareStatement()
	 */
	public function prepare($adapter = null, $forceList = false) {
		// get adapter if needed
		if ($adapter === null) $adapter = Ikarus::getDatabaseManager()->getDefaultAdapter();
		
		// create statement
		return $adapter->prepareStatement($this->__toString(), 0, 0, $forceList);
	}

	/**
	 * Render columns
	 * @return			string
	 */
	protected function renderColumns() {
		// no columns?
		if (!count($this->parts[static::COLUMNS])) return '';

		$columns = array();
		foreach ($this->parts[static::COLUMNS] as $columnEntry) {
			list($correlationName, $column, $alias) = $columnEntry;
			
			if (empty($correlationName))
				$columns[] = $this->getColumnString($column, $alias);
			else
				$columns[] = $this->getColumnString(array($correlationName, $column), $alias);
		}

		return ' ' . implode(', ', $columns);
	}
	
	/**
	 * Render DISTINCT clause
	 * @return			string
	 */
	protected function renderDistinct() {
		if ($this->parts[static::DISTINCT]) return ' '.static::SQL_DISTINCT;
		return '';
	}
	
	/**
	 * Render FOR UPDATE clause
	 * @return			string
	 */
	protected function renderForupdate() {
		if ($this->parts[static::FOR_UPDATE]) return ' '.static::SQL_FOR_UPDATE;
		return '';
	}

	/**
	 * Render FROM clause
	 * @return			string
	 */
	protected function renderFrom() {
		$from = array();

		foreach ($this->parts[static::FROM] as $correlationName => $table) {
			$tmp = '';
			$joinType = ($table['joinType'] == static::FROM ? static::INNER_JOIN : $table['joinType']);

			// add join clause (if applicable)
			if (!empty($from)) $tmp .= ' '.strtoupper($joinType).' ';

			// add table string
			$tmp .= $this->getTableString($table['tableName'], $correlationName);

			// add join conditions (if applicable)
			if (!empty($from) and !empty($table['joinCondition'])) {
				$tmp .= ' ' . static::SQL_ON . ' ' . $table['joinCondition'];
			}

			// add the table name and condition add to the list
			$from[] = $tmp;
		}

		// add the list of all joins
		if (!empty($from)) return ' '.static::SQL_FROM.' '.implode("\n", $from);
		return '';
	}
	
	/**
	 * Render GROUP clause
	 * @return			string
	 */
	protected function renderGroup() {
		if (count($this->parts[static::FROM]) and count($this->parts[static::GROUP])) {
			$group = array();
			foreach ($this->parts[static::GROUP] as $term) {
				$group[] = $term;
			}
			return ' '.static::SQL_GROUP_BY.' '.implode(",\n\t", $group);
		}
		return '';
	}
	
	/**
	 * Render HAVING clause
	 * @return			string
	 */
	protected function renderHaving() {
		if (count($this->parts[static::FROM]) and count($this->parts[static::HAVING])) {
			return ' '.static::SQL_HAVING.' '. implode(' ', $this->parts[static::HAVING]);
		}
		return '';
	}
	
	/**
	 * Render LIMIT OFFSET clause
	 * @return			string
	 */
	protected function renderLimitoffset() {
		$count = 0;
		$offset = 0;

		// offset
		if (!empty($this->parts[static::LIMIT_OFFSET])) $offset = (int) $this->parts[static::LIMIT_OFFSET];

		// limit
		if (!empty($this->parts[static::LIMIT_COUNT])) $count = (int) $this->parts[static::LIMIT_COUNT];

		// get query from default adapter
		if ($count > 0 or $offset > 0) return Ikarus::getDatabaseManager()->getDefaultAdapter()->handleLimitParameter('', $count, $offset);
		return '';
	}
	
	/**
	 * Render ORDER clause
	 * @return			string
	 */
	protected function renderOrder() {
		if (count($this->parts[static::ORDER])) {
			$order = array();
			
			foreach ($this->parts[static::ORDER] as $term) {
				if (is_array($term)) {
					if(is_numeric($term[0]) and strval(intval($term[0])) == $term[0])
						$order[] = (int)trim($term[0]) . ' ' . $term[1];
					else
						$order[] = $term[0].' '.$term[1];
				} else if (is_numeric($term) and strval(intval($term)) == $term)
					$order[] = (int) trim($term);
				else
					$order[] = $term;
			}
			
			return ' '.static::SQL_ORDER_BY.' '.implode(', ', $order);
		}

		return '';
	}
	
	/**
	 * Returns the correct query for this instance
	 * @return			string
	 */
	protected function renderQuery() {
		$sql = static::SQL_SELECT;
		foreach (array_keys(static::$partsInit) as $part) {
			$method = 'render'.ucfirst($part);
			if (method_exists($this, $method)) $sql .= $this->$method();
		}
		return $sql;
	}

	/**
	 * Render UNION query
	 * @return			string
	 */
	protected function renderUnion() {
		$sql = "";
		
		if (count($this->parts[static::UNION]) > 0) {
			$parts = count($this->parts[static::UNION]);
			
			foreach ($this->parts[static::UNION] as $count => $union) {
				list($target, $type) = $union;
				$sql .= $target;
				if ($count < $parts - 1) $sql .= ' '.$type.' ';
			}
		}
		return $sql;
	}

	/**
	 * Render WHERE clause
	 * @return				string
	 */
	protected function renderWhere() {
		if (count($this->parts[static::FROM]) and count($this->parts[static::WHERE])) return ' '.static::SQL_WHERE.' '. implode(' ', $this->parts[static::WHERE]);
		return '';
	}
	
	/**
	 * Resets the complete instance or one part
	 * @param			string			$part
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function reset($part = null) {
		if ($part == null)
			$this->parts = static::$partsInit;
		else if (array_key_exists($part, static::$partsInit))
			$this->parts[$part] = static::$partsInit[$part];
		return $this;
	}
	
	/**
	 * Adds a new table->column association
	 * @param			string			$correlationName
	 * @param			mixed			$columns
	 * @return			void
	 */
	protected function setTableColumns($correlationName, $columns) {
		// unify columns
		if (!is_array($columns)) $columns = array(0 => $columns);

		// default value for correlationName
		if ($correlationName == null) $correlationName = '';
		
		// filter empty values
		$columns = array_filter($columns, function($value) {
			return !empty($value);
		});

		$columnValues = array();
		foreach ($columns as $alias => $column) {
			$currentCorrelationName = $correlationName;
			
			// Check for a column matching "<column> AS <alias>" and extract the alias name
			if (preg_match('~^(.+)\s+'.static::SQL_AS.'\s+(.+)$~i', $column, $matches)) {
				$column = $matches[1];
				$alias = $matches[2];
			}
			
			// Extract correlation name from column name
			if (preg_match('~(.+)\.(.+)~', $column, $matches)) {
				$currentCorrelationName = $matches[1];
				$column = $matches[2];
			}
			
			$columnValues[] = array($currentCorrelationName, $column, (is_string($alias) ? $alias : null));
		}

		if (count($columnValues) > 0) {
			// apply current values to current stack
			foreach ($columnValues as $columnValue) {
				array_push($this->parts[static::COLUMNS], $columnValue);
			}
		}
	}
	
	/**
	 * Adds a UNION clause to the query.
	 * @param			mixed			$select
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function union($select = array(), $type = self::SQL_UNION) {
		// unify select parameter
		if (!is_array($select)) $select = array(0 => $select);

		// validate type
		if (!in_array($type, static::$validUnionTypes)) throw new SystemException("Invalid union type '%s' passed to ".__CLASS__."::union()", $type);

		// execute
		foreach ($select as $target) {
			$this->parts[static::UNION][] = array($target, $type);
		}
		return $this;
	}
	
	/**
	 * Adds a WHERE condition to the query by AND.
	 * @param			string			$condition
	 * @param			int			$type
	 * @return			ikarus\system\database\QueryEditor
	 */
	public function where($condition, $type = self::TYPE_AND) {		
		// search for unions
		if (count($this->parts[static::UNION])) throw new SystemException("Invalid use of ".__CLASS__."::where with UNION");

		$clause = "";
		if ($this->parts[static::WHERE]) $clause .= $this->getType($type).' ';
		$this->parts[static::WHERE][] = $clause."($condition)";
		return $this;
	}

	/**
	 * Returns the query built with this instance
	 * @return			string
	 */
	public function __toString() {
		return $this->renderQuery();
	}
}
?>