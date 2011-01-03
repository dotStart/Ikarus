<?php

/**
 * Manages events
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class EventHandler {

	/**
	 * Contains all EventListeners
	 * @var array<array<array<EventListener>>>
	 */
	protected static $listenerList = null;

	/**
	 * Fires an event
	 * @param	mixed	$class
	 * @param	string	$event
	 */
	public static function fire($class, $event) {
		// load cache
		if (!self::$listenerList) self::loadCache();

		// fire event
		if (isset(self::$listenerList[(is_string($class) ? $class : get_class($class))][$event])) {
			foreach(self::$listenerList[(is_string($class) ? $class : get_class($class))][$event] as $listenerFile) {
				// include listener
				require_once($listenerFile);

				// create instance
				$className = basename($listenerFile, '.class.php');
				$instance = new $className();

				// validate instance
				if (!($instance instanceof $className)) throw new SystemException("Cannot use class '%s' as EventListener", $className);

				// execute listener
				$instance->execute($class, $event);
			}
		}
	}

	/**
	 * Loads the listener cache
	 */
	protected static function loadCache() {
		self::$listenerList = IKARUS::getCache()->get(IKARUS_DIR.'cache/cache.eventListener-'.PACKAGE_ID.'.php', IKARUS_DIR.'lib/system/cache/CacheBuilderEventListener.class.php');
	}
}
?>