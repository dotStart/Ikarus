<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginModifier.class.php');
require_once(CP_DIR.'lib/system/template/Template.class.php');

/**
 * The 'arrayfromlist' modifier generates an associative array out of a key-value list.
 * The list has key-value pairs separated by : with each pair on an own line:
 *
 * Example list:
 * key1:value1
 * key2:value2
 * ...
 *
 * Usage:
 * {$list|arrayfromlist}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginModifierArrayfromlist implements TemplatePluginModifier {
	/**
	 * @see TemplatePluginModifier::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {

		return OptionUtil::parseSelectOptions($tagArgs[0]);
	}
}
?>