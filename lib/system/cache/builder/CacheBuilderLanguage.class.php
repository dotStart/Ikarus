<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;

/**
 * Caches all applications
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderLanguage implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $languageID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_language_variables' => 'languageVariables'));
		$editor->where('languageID = ?');
		$stmt = $editor->prepare();
		$stmt->bind($languageID);
		$resultList = $stmt->fetchList();
		
		$languageVariables = array();
		
		foreach($resultList as $languageVariable) {
			$languageVariables[$languageVariable->variableName] = $languageVariable;
		}
		
		return $languageVariables;
	}
}
?>