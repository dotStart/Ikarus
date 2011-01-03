<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginCompiler.class.php');

/**
 * The 'staticlang' compiler function gets the source of a language variables.
 *
 * Usage:
 * {staticlang}$blah{/staticlang}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginCompilerStaticlang implements TemplatePluginCompiler {
	/**
	 * @see TemplatePluginCompiler::executeStart()
	 */
	public function executeStart($tagArgs, TemplateScriptingCompiler $compiler) {
		$compiler->pushTag('staticlang');

		return "<?php ob_start(); ?>";
	}

	/**
	 * @see TemplatePluginCompiler::executeEnd()
	 */
	public function executeEnd(TemplateScriptingCompiler $compiler) {
		$compiler->popTag('staticlang');
		$hash = StringUtil::getRandomID();
		return "<?php \$_lang".$hash." = ob_get_contents(); ob_end_clean(); echo IKARUS::getLanguage()->get(\$_lang".$hash."); ?>";
	}
}
?>