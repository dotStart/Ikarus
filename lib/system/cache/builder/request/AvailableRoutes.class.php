<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\cache\builder\request;
use ikarus\system\cache\builder\ICacheBuilder;
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
class AvailableRoutes implements ICacheBuilder {

	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName, $additionalParameters) {
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