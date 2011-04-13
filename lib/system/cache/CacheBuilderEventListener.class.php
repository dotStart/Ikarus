<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches event listeners
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CacheBuilderEventListener implements CacheBuilder {

	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($file) {
		// get basename
		$file = basename($file, '.php');

		// remove 'cache.'
		$file = substr($file, stripos($file, '.') + 1);

		// split
		list($file, $packageID) = explode('-', $file);

		// create needed variables
		$data = array();

		// get data
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_event_listener listener
			LEFT JOIN
				ikarus".IKARUS_N."_package_dependency dependency
			ON
				listener.packageID = dependency.packageID
			WHERE
				dependency.packageID = ".$packageID."
			OR
				listener.packageID = ".$packageID;
		$result = IKARUS::getDatabase()->sendQuery($sql);

		while($row = IKARUS::getDatabase()->fetchArray($result)) {
			$data[$row['className']][$row['event']][] = $row['listenerFile'];
		}

		return $data;
	}
}
?>