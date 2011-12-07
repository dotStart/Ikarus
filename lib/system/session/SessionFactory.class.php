<?php
namespace ikarus\system\session;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;
use ikarus\util\StringUtil;

use ikarus\util\DebugUtil;

/**
 * Provides methods for creating new sessions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class SessionFactory {
	
	/**
	 * Contains the namespace path that points to custom session classes
	 * @var			string
	 */
	const SESSION_CLASS_PATH = 'system\\session\\Session';
	
	/**
	 * Creates a new session with the specified abbreviation for the specified application
	 * @param			string			$sessionID
	 * @param			string			$abbreviation
	 * @param			integer			$packageID
	 * @param			string			$environment
	 * @return			void
	 */
	public static function createSession($sessionID, $abbreviation, $packageID, $environment) {
		// no custom session class? exit!
		if (!class_exists($abbreviation.'\\'.static::SESSION_CLASS_PATH)) return false;
		
		// create session instance
		$className = $abbreviation.'\\'.static::SESSION_CLASS_PATH;
		
		$instance = new $className(array(
			'sessionID'			=>	$sessionID,
			'packageID'			=>	$packageID,
			'environment'			=>	$environment
		));
		
		// strict standards
		if (!ClassUtil::isInstanceOf($instance, 'ikarus\\system\\session\\ISession')) throw new StrictStandardException('Session class \'%s\' is not an instance of ikarus\\system\\session\\ISession', $className);
		
		// save
		$sql = "INSERT INTO
				ikarus".IKARUS_N."_session (sessionID, sessionData, ipAddress, userAgent, packageID, environment, abbreviation)
			VALUES
				(?, ?, ?, ?, ?, ?, ?)";
		$stmt = Ikarus::getDatabaseManager()->getDefaultAdapter()->prepareStatement($sql);
		$stmt->bind($sessionID);
		$stmt->bind(serialize($instance));
		$stmt->bind($instance->ipAddress);
		$stmt->bind($instance->userAgent);
		$stmt->bind($packageID);
		$stmt->bind($environment);
		$stmt->bind($abbreviation);
		$stmt->execute();
		
		// update session
		$instance->update();
		
		Ikarus::getComponent('SessionManager')->registerSession($abbreviation, $instance);
	}
	
	/**
	 * Creates a new session ID
	 * @todo			Queries in while loops are not the best way ...
	 * @return			string
	 */
	public static function createSessionID() {
		do {
			$sessionID = StringUtil::getRandomID();
			$sql = "SELECT
					*
				FROM
					ikarus".IKARUS_N."_session
				WHERE
					sessionID = ?";
			$stmt = Ikarus::getDatabaseManager()->getDefaultAdapter()->prepareStatement($sql);
			$stmt->bind($sessionID);
		} while (count($stmt->fetchList()) > 0);
		
		return $sessionID;
	}
}
?>