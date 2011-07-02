<?php
namespace ikarus\system\template;

/**
 * Template functions are identical to template blocks, but they have no closing tag.
 * 
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system.template
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplatePluginFunction {
	
	/**
	 * Executes this template function.
	 * 
	 * @param	array			$tagArgs
	 * @param	Template		$tplObj
	 * @return	string					output
	 */
	public function execute($tagArgs, Template $tplObj);
}
?>