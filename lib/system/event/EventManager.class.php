<?php
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
	 * @param	mixed	$class
	 * @param	string	$event
	 * @return			void
	 */
	public function fire($class, $event) {
		// normal listeners
		if (isset($this->listenerList[$class][$event]))
			foreach($this->listenerList[$class][$event] as $listenerInformation) {
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
								
							$instance->execute($class, $event, $listenerInformation);
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