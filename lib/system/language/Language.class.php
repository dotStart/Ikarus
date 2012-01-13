<?php
namespace ikarus\system\language;
use ikarus\data\DatabaseObject;
use ikarus\system\Ikarus;

/**
 * 
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class Language extends DatabaseObject {
	
	/**
	 * @see ikarus\data\DatabaseObject::$identifierField
	 */
	protected static $identifierField = 'languageID';
	
	/**
	 * @see ikarus\data\DatabaseObject::$tableName
	 */
	protected static $tableName = 'ikarus1_language';
	
	/**
	 * Contains all language variables for this language
	 * @var			array<string>
	 */
	protected $languageVariables = array();
	
	/**
	 * Loads the language cache for this language
	 * @return			void
	 */
	public function loadCache() {
		// performance check
		if (!$this->hasContent) return;
		
		// load resource
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('language-'.$this->languageID, 'language-'.$this->languageID, 'ikarus\\system\\cache\\builder\\CacheBuilderLanguage');
		
		// get information
		$this->languageVariables = Ikarus::getCacheManager()->getDefaultAdapter()->get('language-'.$this->languageID);
	}
	
	/**
	 * Returns the compiled value of a language variable
	 * @param			string			$variableName
	 * @param			boolean			$disableCompilation
	 */
	public function get($variableName, $disableCompilation = false) {
		// return variable name if variable doesn't exist
		if (!isset($this->languageVariables[$variableName])) return $variableName;
		
		// get content
		$content = $this->languageVariables[$variableName]->variableContent;
		
		// return if compilation is disabled
		if ($disableCompilation or !$this->languageVariables[$variableName]->isDynamicVariable or !Ikarus::componentAbbreviationExists('Template')) return $content;
		
		// compile ...
		return $content;
		// TODO: Add compilation here
	}
}
?>