<?php
namespace ikarus\system\exception;
use ikarus\util\StringUtil;

/**
 * This exception will thrown if a database problem occoures
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DatabaseException extends SystemException {

	/**
	 * Contains the database driver where the error has occoured
	 * @var DatabaseDriver
	 */
	protected $databaseDriver = null;

	/**
	 * Contains the version of database server
	 * @var string
	 */
	protected $sqlVersion = null;
	
	/**
	 * Contains an error query (if any)
	 * @var			string
	 */
	protected $errorQuery = null;

	/**
	 * Creates a new instance of DatabaseException
	 */
	public function __construct() {
		// validate arguments
		if (!func_num_args()) die("<strong>FATAL:</strong> Cannot display SystemException: Invalid arguments passed to system exception!");

		// get arguments (sorry for this shit but i would like a c like system exception ;-D)
		$arguments = func_get_args();

		// remove argument1 (DatabaseDriver)
		$this->databaseDriver = $arguments[0];
		unset($arguments[0]);

		// resort
		$arguments = array_merge(array(), $arguments);

		// call parent
		call_user_func_array(array('parent', '__construct'), $arguments);
	}

	/**
	 * Returns the sql type of the active database.
	 * @return	string
	 */
	public function getDatabaseType() {
		return get_class($this->databaseDriver);
	}

	/**
	 * Returns the error description of this exception.
	 * @return	string
	 */
	public function getErrorDesc() {
		return $this->databaseDriver->getErrorDescription();
	}

	/**
	 * Returns the error number of this exception.
	 * @return	integer
	 */
	public function getErrorNumber() {
		return $this->databaseDriver->getErrorNumber();
	}
	
	/**
	 * Returns the sql client version of php installation
	 * @return			string
	 */
	public function getSQLCLientVersion() {
		return $this->databaseDriver->getClientVersion();
	}

	/**
	 * Returns the current sql version of the database.
	 * @return	string
	 */
	public function getSQLVersion() {
		// get version if not defined
		if (!$this->sqlVersion) $this->sqlVersion = $this->databaseDriver->getVersion();

		return $this->sqlVersion;
	}

	/**
	 * @see ikarus\system\exception.SystemException::modifyInformation()
	 */
	public function modifyInformation() {
		parent::modifyInformation();
		
		$this->information['database driver'] = StringUtil::encodeHTML($this->getDatabaseType());
		$this->information['sql error'] = StringUtil::encodeHTML($this->getErrorDesc());
		$this->information['sql error number'] = StringUtil::encodeHTML($this->getErrorNumber());
		$this->information['sql version'] = StringUtil::encodeHTML($this->getSQLVersion());
		$this->information['sql client version'] = StringUtil::encodeHTML($this->getSQLClientVersion());
		$this->information = array_merge($this->information, $this->databaseDriver->getErrorInformation());
		
		if (is_string($this->errorQuery)) {
			$this->additionalInformationElements .= '<h2><a href="javascript:void(0);" onclick="$(\'#errorQuery\').toggle(\'blind\'); $(this).text(($(this).text() == \'+\' ? \'-\' : \'+\'));">+</a>Query</h2>';
			$this->additionalInformationElements .= '<pre id="errorQuery" style="display: none;">'.StringUtil::encodeHTML($this->errorQuery).'</pre>';
			
			$this->hiddenInformation['error query'] = StringUtil::encodeHTML($this->errorQuery);
		}
	}
	
	/**
	 * Sets the query that produces this exception (if any)
	 * @param			string			$query
	 * @return			void
	 */
	public function setErrorQuery($query) {
		$this->errorQuery = $query;
	}
}
?>