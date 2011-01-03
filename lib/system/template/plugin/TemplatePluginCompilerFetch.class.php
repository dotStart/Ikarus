<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplatePluginCompiler.class.php');

/**
 * The 'fetch' compiler function fetches files from the local file system, http, or ftp and displays the content.
 *
 * Usage:
 * {fetch file='x.html'}
 * {fetch file='x.html' assign=var}
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplatePluginCompilerFetch implements TemplatePluginCompiler {
	/**
	 * @see TemplatePluginCompiler::executeStart()
	 */
	public function executeStart($tagArgs, TemplateScriptingCompiler $compiler) {
		if (!isset($tagArgs['file'])) {
			throw new SystemException($compiler->formatSyntaxError("missing 'file' argument in fetch tag", $compiler->getCurrentIdentifier(), $compiler->getCurrentLineNo()), 12001);
		}

		if (isset($tagArgs['assign'])) {
			return "<?php \$this->assign(".$tagArgs['assign'].", @file_get_contents(".$tagArgs['file'].")); ?>";
		}
		else {
			return "<?php echo @file_get_contents(".$tagArgs['file']."); ?>";
		}
	}

	/**
	 * @see TemplatePluginCompiler::executeEnd()
	 */
	public function executeEnd(TemplateScriptingCompiler $compiler) {
		throw new SystemException($compiler->formatSyntaxError("unknown tag {/fetch}", $compiler->getCurrentIdentifier(), $compiler->getCurrentLineNo()), 12003);
	}
}
?>