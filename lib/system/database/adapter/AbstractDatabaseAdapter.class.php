<?php
namespace ikarus\system\database\adapter;
use ikarus\system\database\DatabaseResult;
use ikarus\system\database\DatabaseResultList;
use ikarus\system\exception\DatabaseException;
use ikarus\system\exception\SystemException;

/**
 * Implements default methods for database adapters
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class AbstractDatabaseAdapter implements IDatabaseAdapter {
	
	/**
	 * Contains the name of the database editor that should be used
	 * @var			string
	 */
	const DATABASE_EDITOR_CLASS = 'ikarus\system\database\DatabaseEditor';
	
	/**
	 * Contains the name of the class used for prepared statements
	 * @var			string
	 */
	const PREPARED_STATEMENT_CLASS = 'ikarus\system\database\PreparedStatement';
	
	/**
	 * Contains the current connection object
	 * @var			mixed
	 */
	protected $connection = null;
	
	/**
	 * Contains an instance of IDatabaseEditor
	 * @var			ikarus\system\database\IDatabaseEditor
	 */
	protected $databaseEditor = null;
	
	/**
	 * Contains the current selected database
	 * @var			string
	 */
	protected $databaseName = '';
	
	/**
	 * Contains additional parameters used for connection
	 * @var			array
	 */
	protected $databaseParameters = array();
	
	/**
	 * Contains the hostname where the database server runs (or an ip address)
	 * @var			string
	 */
	protected $hostname = '';
	
	/**
	 * Contains the result set of last query
	 * @var			mixed
	 */
	protected $lastResult = null;
	
	/**
	 * Contains the last executed query
	 * @var			string
	 */
	protected $lastQuery = "";
	
	/**
	 * Contains a list of needed database parameters
	 * @var			array<string>
	 */
	protected $neededDatabaseParameters = array();
	
	/**
	 * Contains the password used to connect
	 * @var			string
	 */
	protected $password = '';
	
	/**
	 * Contains the port where the database server listens
	 * @var			integer
	 */
	protected $port = 0;
	
	/**
	 * Contains the amount of sent queries in this application instance
	 * @var			integer
	 */
	protected $queryCount = 0;
	
	/**
	 * Contains the username used to connect
	 * @var			string
	 */
	protected $user = '';
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::__construct()
	 */
	public function __construct($hostname, $port, $user, $password, $databaseParameters) {
		// proceed arguments
		$this->hostname = $hostname;
		$this->port = $port;
		$this->user = $user;
		$this->password = $password;
		parse_str($databaseParameters, $this->databaseParameters);
		
		// check database parameters
		if (count(array_diff($this->neededDatabaseParameters, array_keys($this->databaseParameters)))) throw new SystemException("Cannot start database adapter %s: Needed database parameters are missing", __CLASS__);
	
		// start connection
		$this->connect();
	}
	
	/**
	 * Creates a connection to database server
	 */
	abstract public function connect();
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::escapeString()
	 */
	public function escapeString($string) {
		return addslashes($string);
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getClientVersion()
	 */
	public function getClientVersion() {
		return 'unknown';
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getDatabaseEditor()
	 */
	public function getDatabaseEditor() {
		if ($this->databaseEditor === null) {
			$className = static::DATABASE_EDITOR_CLASS;
			$this->databaseEditor = new $className($this);
		}
		
		return $this->databaseEditor;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getDatabaseName()
	 */
	public function getDatabaseName() {
		return $this->databaseName;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getErrorDescription()
	 */
	public function getErrorDescription() {
		return '';
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getErrorInformation()
	 */
	public function getErrorInformation() {
		return array();
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getErrorNumber()
	 */
	public function getErrorNumber() {
		return 0;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getInsertID()
	 */
	public function getInsertID($table = null, $field = null) {
		return null;
	}
	
	public function getLastQuery() {
		return $this->lastQuery;
	}
	
	public function getLastResult() {
		return $this->lastResult;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getParameter()
	 */
	public function getParameter($parameter) {
		if (isset($this->databaseParameters[$parameter])) return $this->databaseParameters[$parameter];
		return null;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getQueryCount()
	 */
	public function getQueryCount() {
		return $this->queryCount;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getQuoteDelimiter()
	 */
	public function getQuoteDelimiter() {
		return array('\'', '"');
	}
	
	/**
	 * Returns a result object for use in application
	 * @param			mixed				$result
	 * @param			boolean				$forceList
	 * @return			mixed
	 */
	protected function getResultObject($result, $forceList = false) {
		if (count($result) > 1 or $forceList) {
			return (new DatabaseResultList($result));
		} else {
			foreach($result as $realResult) {
				return (new DatabaseResult($result));
			}
		}
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::getVersion()
	 */
	public function getVersion() {
		return 'unknown';
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::handleLimitParameter()
	 */
	public function handleLimitParameter($query, $limit = 0, $offset = 0) {
		if ($limit != 0) $query .= " LIMIT ".$limit." OFFSET ". $offset;
		return $query;
	}
	
	/**
	 * @see ikarus\system\atabase\adapter.IDatabaseAdapter::isSupported()
	 */
	public static function isSupported() {
		return true;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::prepareStatement()
	 */
	public function prepareStatement($statement, $limit = 0, $offset = 0, $forceList = false) {
		$statement = $this->handleLimitParameter($statement, $limit, $offset);
		
		$className = static::PREPARED_STATEMENT_CLASS;
		return (new $className($this, $statement, $forceList));
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::quote()
	 */
	public function quote($string) {
		return "'".$this->escapeString($string)."'";
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::selectDatabase()
	 */
	public function selectDatabase($databaseName) {
		$stmt = $this->prepareStatement("USE ".$databaseName);
		$stmt->execute();
		
		$this->databaseName = $databaseName;
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::sendQuery()
	 */
	public function sendQuery($sql, $forceList = false) {
		throw new SystemException("The adapter %s is not completely implemented and does not support method %s", get_class($this), __FUNCTION__);
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::setCharset()
	 */
	public function setCharset($charset) {
		try {
			$this->sendQuery("SET NAMES ".$this->quote($charset));
		} Catch (DatabaseException $ex) { }
	}
	
	/**
	 * @see ikarus\system\database\adapter.IDatabaseAdapter::setParameter()
	 */
	public function setParameter($parameter, $value = null) {
		// allow passing arrays of parameters
		if (is_array($parameter)) {
			foreach($parameter as $key => $val) $this->setParameter($key, $val);
			return;
		}
		
		$this->databaseParameters[$parameter] = $value;
	}
	
	/**
	 * Closes the database connection
	 * @return			void
	 */
	abstract public function shutdown();
}
?>