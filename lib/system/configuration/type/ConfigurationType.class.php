<?php
namespace ikarus\system\configuration\type;

/**
 * Defines needed methods for configuration types
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 * @todo		Add needed methods for acp interface etc.
 */
interface ConfigurationType {
	
	/**
	 * Returns the real value of given stored value
	 * @param			string			$value
	 * @return			mixed
	 */
	public static function getRealValue($value);
}
?>