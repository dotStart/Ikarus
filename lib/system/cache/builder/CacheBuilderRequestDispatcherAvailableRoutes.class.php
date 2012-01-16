<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;
use ikarus\util\DependencyUtil;

/**
 * Caches all request dispatcher routes
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderRequestDispatcherAvailableRoutes implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $packageID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_request_route' => 'requestRoute'));
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'requestRoute');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();
		
		$routeList = array();
		
		foreach($resultList as $result) {
			if (!isset($routeList[$result->parameterName])) $routeList[$result->parameterName] = array();
			$routeList[$result->parameterName][$result->routeName] = array('controllerName' => $result->controllerName, 'controllerDirectory' => $result->controllerDirectory);
		}
		
		return $routeList;
	}
}
?>