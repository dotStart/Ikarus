<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/session/Session.class.php');
require_once(IKARUS_DIR.'lib/system/session/SessionEditor.class.php');
require_once(IKARUS_DIR.'lib/system/session/CookieSession.class.php');

/**
 * Creates and identifies sessions
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class SessionFactory {

	/**
	 * Contains an instance of SessionFactory
	 * @var SessionFactory
	 */
	protected static $instance = null;

	/**
	 * Contains the current session object
	 * @var Session
	 */
	protected $session = null;

	/**
	 * Contains the class name of the class that should used for Sessions
	 * @var string
	 */
	protected $sessionClassName = 'CookieSession';

	/**
	 * Creates a new instance of SessionFactory
	 * @deprecated
	 */
	protected function __construct() {
		// start php sessions
		session_start();

		// try to find existing session and create new session if no session exists
		if ($this->loadSession() === false)
			$this->createSession();
	}

	/**
	 * Creates a new session
	 */
	protected function createSession() {
		$this->session = call_user_func(array($this->sessionClassName, 'create'), session_id());
		$this->session->init();
	}

	/**
	 * Returnes an instance of SessionFactory
	 * @return SessionFactory
	 */
	public static function getInstance() {
		if (self::$instance === null)
			self::$instance = new SessionFactory();

		return self::$instance;
	}

	/**
	 * Returnes the current session
	 * @return Session
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * Loads a session from database
	 * @return boolean
	 */
	protected function loadSession() {
		// get sessionID
		$sessionID = session_id();

		// try to find a session in database
		$sql = "SELECT
					data.sessionData AS sessionData
				FROM
					ikarus".IKARUS_N."_session session
				LEFT JOIN
					ikarus".IKARUS_N."_session_data data
				ON
					session.sessionID = data.sessionID
				WHERE
					session.sessionID = ".$sessionID."
				AND
					data.packageID = ".PACKAGE_ID;
		$row = IKARUS::getDatabase()->getFirstRow($sql);

		// no rows found -> return false
		if (!IKARUS::getDatabase()->countRows()) return false;

		// decode session
		$this->session = unserialize($row['sessionData']);
	}
}
?>