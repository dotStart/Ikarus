<?php
// cp imports
require_once(CP_DIR.'lib/system/template/Template.class.php');

/**
 * Modifiers are functions that are applied to a variable in the template
 * before it is displayed or used in some other context.
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplatePluginModifier {
	/**
	 * Executes this modifier.
	 *
	 * @param	array			$tagArgs
	 * @param	Template		$tplObj
	 * @return	string			output
	 */
	public function execute($tagArgs, Template $tplObj);
}
?>