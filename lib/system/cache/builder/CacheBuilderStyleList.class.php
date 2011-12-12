<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;
use ikarus\system\style\Style;
use ikarus\util\DependencyUtil;

/**
 * Caches all controller types
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderStyleList implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $packageID, $environment) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_style' => 'style'));
		$editor->where('environment = ?');
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'style');
		$stmt = $editor->prepare();
		$stmt->bind($environment);
		$resultList = $stmt->fetchList();
		
		$styleList = array();
		
		foreach($resultList as $result) {
			$styleList[] = new Style($result->__toArray());
		}
		
		return $styleList;
	}
}
?>