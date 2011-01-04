<?php
// cp imports
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

/**
 * Block functions encloses a template block and operate on the contents of this block.
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplatePluginBlock {
	/**
	 * Executes this template block.
	 *
	 * @param	array			$tagArgs
	 * @param	string			$blockContent
	 * @param	Template 		$tplObj
	 * @return	string					output
	 */
	public function execute($tagArgs, $blockContent, Template $tplObj);

	/**
	 * Initialises this template block.
	 *
	 * @param	array			$tagArgs
	 * @param	Template		$tplObj
	 */
	public function init($tagArgs, Template $tplObj);

	/**
	 * This function is called before every execution of this block function.
	 *
	 * @param	Template		$tplObj
	 * @return	boolean
	 */
	public function next(Template $tplObj);
}
?>