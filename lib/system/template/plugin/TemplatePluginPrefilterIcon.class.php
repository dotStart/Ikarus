<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginPrefilter.class.php');
require_once(CP_DIR.'lib/system/template/TemplateScriptingCompiler.class.php');

/**
 * The 'icon' prefilter compiles static icon paths.
 *
 * Usage:
 * {icon}iconS.png{/icon}
 *
 * @author		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginPrefilterIcon implements TemplatePluginPrefilter {
	/**
	 * @see TemplatePluginPrefilter::execute()
	 */
	public function execute($sourceContent, TemplateScriptingCompiler $compiler) {
		$ldq = preg_quote($compiler->getLeftDelimiter(), '~');
		$rdq = preg_quote($compiler->getRightDelimiter(), '~');
		$sourceContent = preg_replace("~{$ldq}icon{$rdq}([\w\.]+){$ldq}/icon{$rdq}~", '{literal}<?php echo StyleManager::getStyle()->getIconPath(\'$1\'); ?>{/literal}', $sourceContent);

		return $sourceContent;
	}
}
?>