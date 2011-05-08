<?php
namespace ikarus\system\cache;
use ikarus\system\IKARUS;
use ikarus\system\language\Language;

/**
 * Reads all available languages from database
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CacheBuilderLanguages implements CacheBuilder {

	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($file) {
		// get basename
		$file = basename($file, '.php');

		// remove 'cache.'
		$file = substr($file, stripos($file, '.') + 1);

		// split
		list($packageID, $file) = explode('-', $file);

		// create needed variables
		$data = array();

		// get data
		// build monster query from hell
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_language language
			WHERE
				(
					SELECT
						COUNT(*)
					FROM
						ikarus".IKARUS_N."_language_item item
					LEFT JOIN
						ikarus".IKARUS_N."_package_dependency dependency
					ON
						item.packageID = dependency.dependencyID
					WHERE
						item.languageID = language.languageID
					AND
						(
								item.packageID = ".$packageID."
							OR
								dependency.packageID = ".$packageID."
						)
				)";
		$result = IKARUS::getDatabase()->sendQuery($sql);

		while($row = IKARUS::getDatabase()->fetchArray($result)) {
			$data[] = $row;
		}

		return $data;
	}
}
?>