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
namespace ikarus\system\session;
use ikarus\system\event\session\SessionEventArguments;
use ikarus\system\event\session\manager\ClearSessionsEvent;
use ikarus\system\event\session\manager\LoadedSessionInformationEvent;
use ikarus\system\event\session\manager\RegisterSessionEvent;
use ikarus\system\event\session\factory\SessionIDArguments;
use ikarus\system\event\GenericEventArguments;
use ikarus\system\application\IApplication;
use ikarus\system\application\IConfigurableComponent;
use ikarus\system\database\QueryEditor;
use ikarus\system\exception\ApplicationException;
use ikarus\system\Ikarus;
use ikarus\util\DependencyUtil;
use ikarus\util\HeaderUtil;
use ikarus\util\StringUtil;

/**
 * Manages sessions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class SessionManager implements IConfigurableComponent {

	/**
	 * Contains true if the client does not support cookies
	 * @var			boolean
	 */
	protected static $disableCookies = false;

	/**
	 * Contains all registered session objects
	 * @var			array<ikarus\system\session\ISession>
	 */
	protected $sessions = array();

	/**
	 * Contains the session query parameter used if cookies aren't available
	 * @var			string
	 */
	protected static $sessionQueryParameter = '';

	/**
	 * Boots the session
	 * @param			ikarus\system\application\IApplication			$application
	 * @return			void
	 */
	public function boot($application) {
		try {
			$this->loadSessionInformation($application->getPackageID(), $application);
		} Catch (ApplicationException $ex) {
			throw $ex;
			$this->clearSessions();
			$this->loadSessionInformation($application->getPackageID(), $application);
		}
	}

	/**
	 * Clears all session information
	 * @return			void
	 */
	public function clearSessions() {
		// fire event
		$event = new ClearSessionsEvent(new EmptyEventArguments());
		Ikarus::getEventManager()->fire($event);

		// check for cancelled event
		if ($event->isCancelled()) return;

		// reset session tracking
		HeaderUtil::setCookie('sessionID', '', TIME_NOW - 3600);
		if (isset($_REQUEST['s'])) unset($_REQUEST['s']);
		$this->sessions = array();
	}

	/**
	 * Configures the session instance
	 * @param			ikarus\system\application\IApplication			$application
	 * @return			void
	 */
	public function configure(IApplication $application) {
		$this->boot($application);
	}

	/**
	 * Detects the sessionID from query parameters or cookies (if supported)
	 * @return			string
	 */
	protected static function getSessionID() {
		// disable cookies if not support
		if (HeaderUtil::cookiesSupported() === false) static::$disableCookies = true;

		// get cookies
		if (HeaderUtil::getCookie('sessionID') !== null) return HeaderUtil::getCookie('sessionID');

		// get session parameter
		if (isset($_REQUEST['s'])) return StringUtil::trim($_REQUEST['s']);

		// nothing found
		return null;
	}

	/**
	 * Returns the current session query parameter
	 * @return			string
	 */
	public static function getSessionQueryParameter() {
		return static::$sessionQueryParameter;
	}

	/**
	 * Loads needed sessions to memory
	 * @param			integer			$packageID
	 * @throws			ApplicationException
	 * @return			void
	 */
	protected function loadSessionInformation($packageID, $application) {
		// try to load ikarus session
		$sessionID = static::getSessionID();

		if ($sessionID !== null) {
			$editor = new QueryEditor();
			$editor->from(array('ikarus'.IKARUS_N.'_session' => 'session'));
			$editor->where('sessionID = ?');
			$editor->where('environment = ?');
			DependencyUtil::generateDependencyQuery($application->getPackageID(), $editor, 'session');
			$stmt = $editor->prepare(null, true);

			$stmt->bind($sessionID);
			$stmt->bind($application->getEnvironment());
			$result = $stmt->fetchList();

			// save information
			foreach($result as $session) {
				if (!static::validateRemoteAddress($session->ipAddress, $_SERVER['REMOTE_ADDR'])) throw new ApplicationException('IP Address is not valid for this session');
				if (!static::validateUserAgent($session->userAgent, $_SERVER['HTTP_USER_AGENT'])) throw new ApplicationException('User Agent is not valid for this session');
				$this->registerSession($session->abbreviation, unserialize($session->sessionData));
			}
		}

		// generate new sessionID
		if ($sessionID === null or !count($result)) $sessionID = SessionFactory::createSessionID();

		// create sessions if needed
		if (!$this->sessionExists('ikarus')) SessionFactory::createSession($sessionID, 'ikarus', IKARUS_ID, $application->getEnvironment());
		if (!$this->sessionExists($application->getAbbreviation())) SessionFactory::createSession($sessionID, $application->getAbbreviation(), $application->getPackageID(), $application->getEnvironment());

		// validate ikarus session
		if (!$this->sessionExists('ikarus')) throw new ApplicationException('Ikarus session was not created');

		// save sessionID
		static::saveSessionID($sessionID);

		// fire event
		Ikarus::getEventManager()->fire(new LoadedSessionInformationEvent(new SessionIDArguments($sessionID)));
	}

	/**
	 * Registers a new session
	 * @param			string			$abbreviation
	 * @param			ISession		$sessionInstance
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function registerSession($abbreviation, ISession $sessionInstance) {
		// fire event
		$event = new RegisterSessionEvent(new SessionEventArguments($sessionInstance, $abbreviation));
		Ikarus::getEventManager()->fire($event);

		// cancellable event (allows instance replacement)
		if ($event->isCancelled() and $event->getReplacement() === null)
			return;
		elseif ($event->isCancelled())
			$sessionInstance = $event->getReplacement();

		// strict standard
		if ($this->sessionExists($abbreviation)) throw new StrictStandardException("A session with abbreviation '%s' does already exist", $abbreviation);

		// save
		$this->sessions[$abbreviation] = $sessionInstance;
	}

	/**
	 * Saves the sessionID at client
	 * @param			string			$sessionID
	 * @return			void
	 */
	protected static function saveSessionID($sessionID) {
		// save cookies
		HeaderUtil::setCookie('sessionID', $sessionID);

		// save query parameter information if needed
		if (!HeaderUtil::cookiesSupported()) static::$sessionQueryParameter = 's='.urlencode($sessionID);
	}

	/**
	 * Checks whether a session with specified abbreviation exists
	 * @param			string			$abbreviation
	 * @return			boolean
	 */
	public function sessionExists($abbreviation) {
		return isset($this->sessions[$abbreviation]);
	}

	/**
	 * Validates ip addresses for sessions
	 * @param			string			$checkAddress
	 * @param			string			$currentAddress
	 * @return			boolean
	 */
	protected function validateRemoteAddress($checkAddress, $currentAddress) {
		// checks disabled?
		if (!Ikarus::getConfiguration()->get('security.general.ipCheckEnabled')) return true;

		// for performance ;-)
		if ($checkAddress == $currentAddress) return true;

		// valid IP?
		if (filter_var($currentAddress, FILTER_VALIDATE_IP) === false) return false;

		if (filter_var($currentAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) { // IPv6
			// split addresses
			$checkAddress = explode(':', $checkAddress);
			$currentAddress = explode(':', $currentAddress);

			// same ip version?
			if (count($currentAddress) <= 0) return false;

			// loop
			foreach($checkAddress as $key => $block) {
				// block maximum
				if ($key > Ikarus::getConfiguration()->get('security.general.ip6CheckMaximumBlockCount')) break;

				// check
				if (!isset($currentAddress[$key]) or $currentAddress[$key] != $checkAddress[$key]) return false;
			}

			return true;
		} else { // IPv4
			// split addresses
			$checkAddress = explode('.', $checkAddress);
			$currentAddress = explode('.', $currentAddress);

			// same ip version?
			if (count($currentAddress) <= 0) return false;

			// loop
			foreach($checkAddress as $key => $block) {
				// block maximum
				if ($key > Ikarus::getConfiguration()->get('security.general.ip4CheckMaximumBlockCount')) break;

				// check
				if (!isset($currentAddress[$key]) or $currentAddress[$key] != $checkAddress[$key]) return false;
			}

			return true;
		}
	}

	/**
	 * Validates user agents for sessions
	 * @param			string			$sessionUserAgent
	 * @param			string			$currentUserAgent
	 * @return			boolean
	 */
	public static function validateUserAgent($sessionUserAgent, $currentUserAgent) {
		// checks disabled?
		if (!Ikarus::getConfiguration()->get('security.general.userAgentCheckEnabled')) return true;

		return ($sessionUserAgent == $currentUserAgent);
	}
}
?>