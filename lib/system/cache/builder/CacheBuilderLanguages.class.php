<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;
use ikarus\system\language\Language;
use ikarus\util\DependencyUtil;

/**
 * Caches the css definitions of a style
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderLanguages implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $packageID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_language' => 'language'));
		$editor->where('isEnabled = 1');
		$editor->where('hasContent = 1');
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'language');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();
		
		$languageList = array();
		
		foreach($resultList as $language) {
			$languageList[] = new Language($language->__toArray());
		}
		
		return $languageList;
	}
}
?>