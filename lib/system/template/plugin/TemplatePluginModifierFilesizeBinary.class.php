<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginModifier.class.php');
require_once(CP_DIR.'lib/system/template/Template.class.php');

/**
 * The 'filesize' modifier formats a filesize (binary) (given in bytes).
 *
 * Usage:
 * {$string|filesizeBinary}
 * {123456789|filesizeBinary}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginModifierFilesizeBinary implements TemplatePluginModifier {
	/**
	 * @see TemplatePluginModifier::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {
		return FileUtil::formatFilesizeBinary($tagArgs[0]);
	}
}
?>