<?php
namespace ikarus\system\option\type;

/**
 * Defines default methods for option types
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface OptionType {

	/**
	 * Formates the given value for options.inc.php
	 * @param	mixed	$value
	 */
	public static function formatOptionValue($value);

	// TODO: Add validation

	// TODO: Add backend template shit
}
?>