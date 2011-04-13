<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a session row
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class Session extends DatabaseObject {

	/**
	 * Contains additional session variables
	 * @var array
	 */
	protected $sessionVariables = array();

	/**
	 * Contains a UserProfile
	 * @var User
	 */
	protected $user = null;

	/**
	 * Creates a new instance of Session
	 * @param	string	$sessionID
	 * @param	array	$row
	 */
	public function __construct($sessionID, $row = null) {
		if ($sessionID !== null) {
			$sql = "SELECT
					session.*,
					sessionData.sessionData as sessionData
				FROM
					ikarus".IKARUS_N."_session session
				LEFT JOIN
					ikarus".IKARUS_N."_session_data SessionData
				ON
					session.sessionID = sessionData.sessionID
				WHERE
					session.sessionID = '".escapeString($sessionID)."'";
			$row = IKARUS::getDB()->getFirstRow($sql);
		}

		parent::__construct($row);
	}

	/**
	 * Registeres a new session variable
	 * @param	string	$var
	 * @param	mixed	$value
	 */
	public function register($var, $value) {
		$this->sessionVariables[$var] = $value;
	}

	/**
	 * @see lib/data/DatabaseObject::__get()
	 */
	public function __get($variable) {
		try {
			$val = parent::__get($variable);
			return $val;
		} catch (SystemException $ex) {
			// ignore
		}

		// handle session variables
		if (isset($this->sessionVariables[$variable])) return $this->sessionVariables[$variable];

		// handle non existent properties
		throw new SystemException("Property '%s' does not exist in class %s", $variable, get_class($this));
	}
}
?>