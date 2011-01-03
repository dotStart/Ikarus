<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplateScriptingCompiler.class.php');

/**
 * Compiler functions are called during the compilation of a template.
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplatePluginCompiler {
	/**
	 * Executes the start tag of this compiler function.
	 *
	 * @param	array				$tagArgs
	 * @param	TemplateScriptingCompiler	$compiler
	 * @return	string						php code
	 */
	public function executeStart($tagArgs, TemplateScriptingCompiler $compiler);

	/**
	 * Executes the end tag of this compiler function.
	 *
	 * @param	TemplateScriptingCompiler	$compiler
	 * @return	string						php code
	 */
	public function executeEnd(TemplateScriptingCompiler $tplObj);
}
?>