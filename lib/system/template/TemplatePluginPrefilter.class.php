<?php
// cp imports
require_once(CP_DIR.'lib/system/template/TemplateScriptingCompiler.class.php');

/**
 * Prefilters are used to process the source of the template immediately before compilation.
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplatePluginPrefilter {
	/**
	 * Executes this prefilter.
	 *
	 * @param	string				$sourceContent
	 * @param	TemplateScriptingCompiler 	$compiler
	 * @return 	string				$sourceContent
	 */
	public function execute($sourceContent, TemplateScriptingCompiler $compiler);
}
?>