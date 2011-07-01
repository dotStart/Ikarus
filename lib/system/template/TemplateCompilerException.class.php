<?php
namespace ikarus\system\template;
use ikarus\system\exception\SystemException;

/**
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class TemplateCompilerException extends SystemException {
	
	/**
	 * Contains the TemplateCompiler instance
	 * @var			TemplateCompiler
	 */
	protected $compilerObj = null;
	
	/**
	 * Creates a new instance of type TemplateCompilerException
	 * @param		TemplateCompiler			$compilerObj
	 * @param		string					$message
	 */
	public function __construct(TemplateCompiler $compilerObj, $message) {
		$this->compilerObj = $compilerObj;
		
		// remove first argument
		$arguments = array_shift(func_get_args());
		
		// redirect
		call_user_func_array(array('parent', '__construct'), $arguments);
	}
}
?>