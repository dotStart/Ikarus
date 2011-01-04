<?php
// cp imports
require_once(IKARUS_DIR.'lib/system/template/TemplatePluginModifier.class.php');
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

/**
 * The 'fulldate' modifier formats a unix timestamp.
 * Uses DateUtil::formatTime to format the given time timestamp.
 *
 * Usage:
 * {$timestamp|fulldate}
 * {"132845333"|fulldate}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginModifierFulldate implements TemplatePluginModifier {
	/**
	 * @see TemplatePluginModifier::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {
		return DateUtil::formatTime(null, $tagArgs[0], false);
	}
}
?>