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
namespace ikarus\system\cache\builder\event;
use ikarus\system\cache\builder\ICacheBuilder;
use ikarus\system\database\QueryEditor;
use ikarus\util\DependencyUtil;

/**
 * Caches all event listeners
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class EventListener implements ICacheBuilder {

	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName, $additionalParameters) {
		list($resourceName, $packageID) = explode('-', $resourceName);

		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_event_listener' => 'listener'));
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'listener');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();

		$listenerList = array();

		foreach($resultList as $result) {
			if (!isset($listenerList[$result->className])) $listenerList[$result->className] = array();
			if (!isset($listenerList[$result->className][$result->eventName])) $listenerList[$result->className][$result->eventName] = array();
			$listenerList[$result->className][$result->eventName][] = $result;
		}

		return $listenerList;
	}
}
?>