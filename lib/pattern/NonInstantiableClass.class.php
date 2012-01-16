<?php
namespace ikarus\pattern;
use ikarus\system\exception\StrictStandardException;

/**
 * Non instantiable class pattern (Classes who inerhit from this will never have an instance)
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class NonInstantiableClass {
	
	/**
	 * A protected construct method
	 */
	protected function __construct() { }
	
	/**
	 * A protected clone method
	 */
	protected function __clone() { }
	
	/**
	 * Disallows serialize()
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function __sleep() {
		throw new StrictStandardException("It's not allowed to serialize singletons");
	}
}
?>