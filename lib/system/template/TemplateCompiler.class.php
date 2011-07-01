<?php
namespace ikarus\system\template;

/**
 * Defines default methods for template compilers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface TemplateCompiler {
	
	/**
	 * Creates a new instance of type TemplateCompiler
	 * @param		array<string>			$templateDirs
	 * @param		string				$templateCompileDir
	 * @throws		TemplateCompilerException
	 */
	public function __construct($templateDirs = array(), $templateCompileDir = null);
	
	/**
	 * Assignes a variable
	 * @param		mixed				$variable
	 * @param		mixed				$value
	 * @return		void
	 */
	public function assign($variable, $value = null);
	
	/**
	 * Fetches a template
	 * @param		string				$templateName
	 * @throws		TemplateCompilerException
	 * @return		string
	 */
	public function fetch($templateName);
	
	/**
	 * Compiles the given template string
	 * @param		string				$templateString
	 * @return		string
	 */
	public function fetchString($templateString);
	
	/**
	 * Returnes an instance of type TemplateCompiler
	 * @return		TemplateCompiler
	 */
	public static function getInstance();
}
?>