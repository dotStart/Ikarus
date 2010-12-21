<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginPrefilter.class.php');

/**
 * The 'lang' prefilter compiles static language variables.
 * Dynamic language variables will catched by the 'lang' compiler function.
 * It is recommended to use static language variables.
 * 
 * Usage:
 * {lang}foo{/lang}
 * {lang}lang.foo.bar{/lang}
 *
 * @author 	Marcel Werk, System-Modifications by Akkarin
 * @copyright	2001-2009 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.template.plugin
 * @category 	Community Framework
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