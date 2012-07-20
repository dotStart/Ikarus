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
namespace ikarus\system\event;
use ikarus\system\exception\StrictStandardException;

use ikarus\system\Ikarus;
use ikarus\system\exception\SystemException;
use ikarus\util\ClassUtil;

/**
 * Manages events
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class EventManager {

	/**
	 * Contains all EventListeners
	 * @var array
	 */
	protected $listenerList = null;
	
	/**
	 * Contains a list of listener instances
	 * @var	array<EventListener>
	 */
	protected $listenerInstances = array();
	
	/**
	 * Creates a new instance of type EventManager
	 * @param			integer			$packageID
	 */
	public function __construct($packageID = IKARUS_ID) {
		$this->loadCache($packageID);
	}
	
	/**
	 * Alias for ikarus\system\event.EventManager::fire()
	 * @see	ikarus\system\event.EventManager::fire()
	 */
	public function fireSimpleEvent($className) {
		// construct a new instance of event
		$event = new $className(new EventArguments());
		return $this->fire($event);
	}

	/**
	 * Fires an event
	 * @param			IEvent			$class
	 * @param			string			$eventClass
	 * @return			void
	 */
	public function fire(IEvent $event, $eventClass = null) {
		// get eventClass (if not already set)
		if ($eventClass === null) $eventClass = get_class($event);
		
		// strict standards
		if (get_class($event) != $eventClass and !ClassUtil::isInstanceOf($event, $eventClass)) throw new StrictStandardException('"%s" has to be a parent of "%s" in case to use it as alias', $eventClass, get_class($event));
		
		// normal listeners
		if (isset($this->listenerList[$eventClass]))
			foreach($this->listenerList[$eventClass] as $listenerInformation) {
				$className = $listenerInformation->listenerClass;
				
				// get listener instance
				if (!isset($this->listenerInstances[$listenerInformation->listenerClass]))
					$instance = $this->listenerInstances[$listenerInformation->listenerClass] = new $className();
				else
					$instance = $this->listenerInstances[$listenerInformation->listenerClass];
					
				$instance->execute($event, $listenerInformation);
			}
		
		// fire parents (if any)
		$parents = ClassUtil::getParents($event);
		
		foreach($parents as $parent) {
			$this->fire($event, $parent);
		}
	}

	/**
	 * Loads the listener cache
	 * @param			integer			$packageID
	 * @return			void
	 */
	protected function loadCache($packageID) {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('eventListener-'.$packageID, 'eventListener-'.$packageID, 'ikarus\system\cache\builder\CacheBuilderEventListener');
		$this->listenerList = Ikarus::getCacheManager()->getDefaultAdapter()->get('eventListener-'.$packageID);
	}
}
?>