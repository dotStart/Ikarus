<?php
require_once(IKARUS_DIR.'lib/system/session/Session.class.php');

/**
 * Represents a session and provides methods fro creating new, updating or deleting sessions
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class SessionEditor extends Session {

	/**
	 * Creates a new instance of SessionEditor
	 * @param	string	$sessionID
	 * @param	array	$row
	 * @param	boolean	$isNew
	 */
	public function __construct($sessionID, $row = null, $isNew = false) {
		// call parent method
		parent::__construct($sessionID, $row);

		// handle additional arguments
		if ($isNew)
			$this->update(true);
	}

	/**
	 * Creates a new session
	 * @param	string	$sessionID
	 * @return SessionEditor
	 */
	public static function create($sessionID) {
		// get additional fields
		$additionalFields = self::getAdditionalSessionInformation(true);

		$sql = "INSERT INTO
					ikarus".IKARUS_N."_session (sessionID, ".implode(',', array_keys($additionalFields)).")
				VALUES
					('".escapeString($sessionID)."', ".implode("','", array_map('escapeString', $additionalFields))."')";
		IKARUS::getDatabase()->sendQuery($sql);

		return new SessionEditor($sessionID, null, true);
	}

	/**
	 * Updates a session row
	 * @param	boolean	$isNew
	 */
	public function update($isNew = false) {
		// update session table
		$additionalFields = array();

		// get basic additional session information
		foreach(self::getAdditionalSessionInformation(true) as $key => $val) {
			$additionalFields[] = $key." = '".escapeString($val)."'";
		}

		// update session row
		$sql = "UPDATE
					ikarus".IKARUS_N."_session
				SET
					".implode(',', $additionalFields)."
				WHERE
					sessionID = ".$this->sessionID;
		WCF::getDB()->sendQuery($sql);

		// add additional session information
		if ($isNew) {
			// get additional session information
			foreach(self::getAdditionalSessionInformation() as $key => $val) {
				$this->register($key, $val);
			}

			// insert new data row
			$sql = "INSERT INTO
						ikarus".IKARUS_N."_session_data (sessionID, sessionData)
					VALUES
						('".escapeString($this->sessionID)."', '".escapeString(serialize($this))."')";
			IKARUS::getDatabase()->sendQuery($sql);
		} else {
			// serialize and update
			$data = serialize($this);

			$sql = "UPDATE
						ikarus".IKARUS_N."_session_data
					SET
						sessionData = '".escapeString($data)."'
					WHERE
						sessionID = '".escapeString($this->sessionData)."'";
			IKARUS::getDatabase()->sendQuery($sql);
		}
	}

	/**
	 * Deletes a session row
	 * @param	string	$sessionID
	 */
	protected static function delete($sessionID) {
		$sql = "DELETE FROM
					ikarus".IKARUS_N."_session
				WHERE
					sessionID = '".escapeString($sessionID)."'";
		IKARUS::getDatabase()->sendQuery($sql);

		$sql = "DELETE FROM
					ikarus".IKARUS_N."_session_data
				WHERE
					sessionID = '".escapeString($sessionID)."'";
		IKARUS::getDatabase()->sendQuery($sql);
	}

	/**
	 * Returnes additional fields for sessions
	 * @param	boolean	$basicInformation
	 */
	protected static function getAdditionalSessionInformation($basicInformation = false) {
		// create needed variables
		$additionalInformation = array();

		$additionalInformation['IP'] = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		$additionalInformation['userAgent'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');

		if (!$basicInformation) {
			$additionalInformation['requestMethod'] = (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '');
			$additionalInformation['requestURI'] = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		}

		return $additionalInformation;
	}
}
?>