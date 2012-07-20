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
	 * @var array<array<array<EventListener>>>
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
	 * Fires an event
	 * @param			mixed			$class
	 * @param			string			$event
	 * @param			mixed			$parents
	 * @return			void
	 */
	public function fire($class, $event, $parents = null) {
		$fireClassName = (is_string($class) ? $class : get_class($class));
		
		// normal listeners
		if (isset($this->listenerList[$fireClassName][$event]))
			foreach($this->listenerList[$fireClassName][$event] as $listenerInformation) {
				$className = $listenerInformation->listenerClass;
				
				// get listener instance
				if (!isset($this->listenerInstances[$listenerInformation->listenerClass]))
					$instance = $this->listenerInstances[$listenerInformation->listenerClass] = new $className();
				else
					$instance = $this->listenerInstances[$listenerInformation->listenerClass];
					
				$instance->execute($class, $event, $listenerInformation);
			}
		
		// inherited listeners
		foreach($this->listenerList as $targetClass => $events)
			if (array_key_exists($event, $events) and ClassUtil::isInstanceOf($class, $targetClass))
				foreach($events as $eventName => $listeners)
					if ($eventName == $event)
						foreach($listeners as $listenerInformation) {
							if (!$listenerInformation->inherit) continue;
							
							$className = $listenerInformation->listenerClass;
							
							// get listener instance
							if (!isset($this->listenerInstances[$listenerInformation->listenerClass]))
								$instance = $this->listenerInstances[$listenerInformation->listenerClass] = new $className();
							else
								$instance = $this->listenerInstances[$listenerInformation->listenerClass];
								
							// strict standards
							if (!ClassUtil::isInstanceOf($instance, 'ikarus\\system\\event\\IEventListener')) throw new StrictStandardException("Cannot use class '%s' as event listener", $listenerInformation->listenerClass);
								
							$instance->execute($class, $event, $listenerInformation);
						}
		
		// call parents
		if ($parents !== null) {
			// single parent
			if (!is_array($parents)) $parents = array($parents);
			
			// fire all parents
			foreach($parents as $parent) {
				$this->fire($class, $parent);
			}
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