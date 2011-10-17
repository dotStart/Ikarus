<?php
namespace ikarus\system\application;
use ikarus\system\request\CommandDispatcher;

/**
 * Implements an application that loads components that are often needed in cli applications
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class AbstractCliApplication extends AbstractApplication {
	
	/**
	 * @see ikarus\system\application.AbstractApplication::boot()
	 */
	public function boot() {
		parent::boot();
		
		CommandDispatcher::getInstance()->dispatch($this, $argv);
	}
}
?>