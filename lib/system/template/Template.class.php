<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/template/Smarty.class.php');

/**
 * Template wrapper
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class Template extends Smarty {
	
	/**
	 * @see Smarty::__construct()
	 */
	public function __construct($templateDirs = array()) {
		parent::__construct();
		
		$this->setTemplateDir($templateDirs);
	}
	
	/**
	 * @see Smarty::fetch()
	 */
	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false) {
		parent::fetch($template.'.tpl', $cache_id, $compile_id, $parent, $display);
	}
}
?>