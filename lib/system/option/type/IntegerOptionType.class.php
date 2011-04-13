<?php
// includes
require_once(IKARUS_DIR.'lib/system/option/type/OptionType.class.php');

/**
 * Option type for integer values
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class IntegerOptionType implements OptionType {

	/**
	 * @see OptionType::formatOptionValue()
	 */
	public static function formatOptionValue($value) {
		return intval($value);
	}
}
?>