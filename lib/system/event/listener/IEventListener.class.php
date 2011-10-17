<?php
namespace ikarus\system\event\listener;

/**
 * Defines default methods for event listeners
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IEventListener {
	
	/**
	 * Executes something on recognized event
	 * @param			mixed			$eventObj
	 * @param			string			$eventName
	 * @return			void
	 */
	public function execute($eventObj, $eventName);
}
?>