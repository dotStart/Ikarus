<?php
// includes
require_once(IKARUS_DIR.'lib/system/option/type/OptionType.class.php');

/**
 * Option type for string values
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TextOptionType implements OptionType {
	
	/**
	 * @see OptionType::formatOptionValue()
	 */
	public static function formatOptionValue($value) {
		// escape value
		$value = str_replace("'", "\'", $value);
		$value = str_replace("\\", "\\\\", $value);
		
		// add quotes
		$value = "'".$value."'";
		
		return $value;
	}
}
?>