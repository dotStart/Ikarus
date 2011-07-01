<?php
namespace ikarus\system\template;

/**
 * Represents a compiled template and provides methods for execution
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CompiledTemplate {
	
	/**
	 * Contains the path to compiled template
	 * @var			string
	 */
	protected $compiledTemplatePath = '';
	
	/**
	 * Contains the name of the template (Without suffix)
	 * @var			string
	 */
	protected $templateName = '';
	
	/**
	 * Contains the complete path of the template
	 * @var			string
	 */
	protected $templatePath = '';
	
	/**
	 * Contains a list of assigned variables
	 * @var			array
	 */
	protected $variables = array();
	
	/**
	 * Creates a new instance of type CompiledTemplate
	 * @param		string			$compiledTemplatePath
	 * @param		string			$templateName
	 * @param		string			$templatePath
	 * @param		array			$variables
	 */
	public function __construct($compiledTemplatePath, $templateName, $templatePath, &$variables) {
		$this->compiledTemplatePath = $compiledTemplatePath;
		$this->templateName = $templateName;
		$this->templatePath = $templatePath;
		$this->variables = $variables;
	}
	
	/**
	 * Executes the template
	 * @return		void
	 */
	public function execute() {
		// validate file
		$this->validateCompiledTemplate();
		
		// execute
		include_once($this->compiledTemplatePath);
	}
	
	/**
	 * Returnes the template output as string
	 * @return		string
	 */
	public function getOutputs() {
		// validate file
		$this->validateCompiledTemplate();
		
		// start output buffer
		ob_start();
		
		// execute template
		include_once($this->compiledTemplatePath);
		
		// stop buffer and get outputs
		return ob_get_clean();
	}
	
	/**
	 * Validates the compiled template
	 * @throws		TemplateException
	 * @return		boolean
	 */
	protected function validateCompiledTemplate() {
		// file exists?
		if (!file_exists($this->compiledTemplatePath)) throw new TemplateException("Could not load compiled template '%s': File does not exist", $this->compiledTemplatePath);
		
		// is readable?
		if (!is_readable($this->compiledTemplatePath)) throw new TemplateException("Could not load compiled template '%s': File is not readable", $this->compiledTemplatePath);
		
		// end
		return true;
	}
}
?>