<?php
namespace ikarus\system\cache\builder;
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
class CacheBuilderEventListener implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $packageID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_event_listener' => 'listener'));
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'listener');
		$stmt = $editor->prepare(null, true);
		$resultList = $stmt->execute();
		
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