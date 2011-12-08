<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;
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
class CacheBuilderRequestDispatcherControllerTypes implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $packageID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_request_controller_type' => 'controllerType'));
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'controllerType');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();
		
		$typeList = array();
		
		foreach($resultList as $result) {
			$typeList[$result->parameterName] = $result->controllerDirectory;
		}
		
		return $typeList;
	}
}
?>