<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginModifier.class.php');
require_once(CP_DIR.'lib/system/template/Template.class.php');

/**
 * The 'datediff' modifier calculates the difference between two unix timestamps.
 *
 * Usage:
 * {$timestamp|datediff}
 * {"123456789"|datediff:$timestamp}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginModifierDatediff implements TemplatePluginModifier {
	/**
	 * @see TemplatePluginModifier::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {
		// get timestamps
		if (!isset($tagArgs[1])) $tagArgs[1] = TIME_NOW;
		$start = min($tagArgs[0], $tagArgs[1]);
		$end = max($tagArgs[0], $tagArgs[1]);

		return DateUtil::diff($start, $end, 'string');
	}
}
?>