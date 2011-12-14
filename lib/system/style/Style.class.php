<?php
namespace ikarus\system\style;
use ikarus\data\DatabaseObject;
use ikarus\system\Ikarus;
use ikarus\util\StringUtil;

/**
 * Represents a style
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 * @todo		Add methods
 */
class Style extends DatabaseObject {
	
	/**
	 * Contains a list of all css definitions in this style
	 * @var			ikarus\system\database\DatabaseResultList
	 */
	protected $cssDefinitions = array();
	
	/**
	 * @see ikarus\data\DatabaseObject::$identifierField
	 */
	protected static $identifierField = 'styleID';
	
	/**
	 * @see ikarus\data\DatabaseObject::$tableName
	 */
	protected static $tableName = 'ikarus1_style';
	
	/**
	 * Builds a minified version of style's css code
	 * @return			string
	 */
	public function buildCssCode() {
		$mediaQueries = array();
		$css = '';
		
		// raw processing
		foreach($this->cssDefinitions as $definition) {
			// stop if css definition is disabled
			if (!$definition->isEnabled) continue;
			
			// save to media query
			if (!isset($mediaQueries[$definition->cssMediaQuery])) $mediaQueries[$definition->cssMediaQuery] = array();
			$mediaQueries[$definition->cssMediaQuery][] = $definition;
		}
		
		// process media queries
		foreach($mediaQueries as $mediaQuery => $definitions) {
			// add media query
			$css .= '@media '.$mediaQuery."{\n\t";
			
			// loop through definitions
			foreach($definitions as $definition) {
				// add comment
				$css .= '/* '.$definition->definitionComment." */\n\t";
				
				// add selector
				$css .= $definition->cssSelector.'{';
				
				// add content (without newlines)
				$css .= StringUtil::replace("\n", '', $definition->cssCode);
				
				// add ending brace
				$css .= "}\n\t";
			}
			
			// add close brace
			$css .= '}';
		}
		
		return $css;
	}
	
	/**
	 * Loads the css definition cache
	 * @return			void
	 */
	public function loadCache() {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('style-'.$this->styleID, 'style-'.$this->styleID, 'ikarus\\system\\cache\\builder\\CacheBuilderStyle');
		
		$this->cssDefinitions = Ikarus::getCacheManager()->getDefaultAdapter()->get('style-'.$this->styleID);
	}
}
?>