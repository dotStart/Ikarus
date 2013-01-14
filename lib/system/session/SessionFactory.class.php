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
use ikarus\system\exception\StrictStandardException;
use ikarus\system\event\session\factory\CreatedSessionIDEvent;
use ikarus\system\event\session\factory\CreateSessionEvent;
use ikarus\system\event\session\factory\SessionIDArguments;
use ikarus\system\event\session\SessionEventArguments;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;
use ikarus\util\StringUtil;

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
	 * @api
	 */
	public static function createSession($sessionID, $abbreviation, $packageID, $environment) {
		// no custom session class? exit!
		if (!class_exists($abbreviation.'\\'.static::SESSION_CLASS_PATH)) return false;

		// create session instance
		$className = $abbreviation.'\\'.static::SESSION_CLASS_PATH;

		$instance = new $className(array(
			'abbreviation'			=>	$abbreviation,
			'sessionID'			=>	$sessionID,
			'packageID'			=>	$packageID,
			'environment'			=>	$environment
		));

		// fire event
		$event = new CreateSessionEvent(new SessionEventArguments($instance));
		Ikarus::getEventManager()->fire($event);

		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new MissingDependencyException('No replacement has been supplied to %s', 'CreateSessionEvent');
		elseif ($event->isCancelled())
			$instance = $event->getReplacement();

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
	 * @api
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

		// fire event
		Ikarus::getEventManager()->fire(new CreatedSessionIDEvent(new SessionIDArguments($sessionID)));

		return $sessionID;
	}
}
?>