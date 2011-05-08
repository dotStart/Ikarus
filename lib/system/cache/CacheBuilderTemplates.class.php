<?php
namespace ikarus\system\cache;
use ikarus\system\IKARUS;

/**
 * Reads all templates from database
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CacheBuilderTemplates implements CacheBuilder {

	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($file) {
		// get basename
		$file = basename($file, '.php');

		// remove 'cache.'
		$file = substr($file, stripos($file, '.') + 1);

		// split
		$information = explode('-', $file);

		if (count($information) == 3) {
			$prefix = $information[0].'_';
			$packageID = $information[2];
		} else {
			$prefix = '';
			if (isset($information[1]))
			$packageID = $information[1];
			else
			$packageID = PACKAGE_ID;
		}

		// create needed variables
		$data = array();

		// get data
		// build monster query from hell
		$sql = "SELECT
				template.templateName AS templateName,
				template.packageID AS packageID
			FROM
				ikarus".IKARUS_N."_".$prefix."template template
			LEFT JOIN
				ikarus".IKARUS_N."_package_dependency dependency
			ON
				template.packageID = dependency.dependencyID
			WHERE
				(
						template.packageID = ".$packageID."
					OR
						dependency.packageID = ".$packageID."
				)";
		$result = IKARUS::getDatabase()->sendQuery($sql);

		while($row = IKARUS::getDatabase()->fetchArray($result)) {
			$data[$row['templateName']] = $row['packageID'];
		}

		return $data;
	}
}
?>