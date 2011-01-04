<?php
// cp imports
require_once(IKARUS_DIR.'lib/system/template/TemplatePluginPrefilter.class.php');

/**
 * The 'lang' prefilter compiles static language variables.
 * Dynamic language variables will catched by the 'lang' compiler function.
 * It is recommended to use static language variables.
 *
 * Usage:
 * {lang}foo{/lang}
 * {lang}lang.foo.bar{/lang}
 *
 * @author 		Marcel Werk, System-Modifications by Akkarin
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginPrefilterLang implements TemplatePluginPrefilter {
	/**
	 * @see TemplatePluginPrefilter::execute()
	 */
	public function execute($sourceContent, TemplateScriptingCompiler $compiler) {
		$ldq = preg_quote($compiler->getLeftDelimiter(), '~');
		$rdq = preg_quote($compiler->getRightDelimiter(), '~');
		$sourceContent = preg_replace("~{$ldq}lang{$rdq}([\w\.]+){$ldq}/lang{$rdq}~e", 'CP::getLanguage()->get(\'$1\')', $sourceContent);

		return $sourceContent;
	}
}
?>