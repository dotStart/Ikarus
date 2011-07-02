<?php
namespace ikarus\system\template;

/**
 * Prefilters are used to process the source of the template immediately before compilation.
 * 
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system.template
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 * @todo		Fix Exceptions
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