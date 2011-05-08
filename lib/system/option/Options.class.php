<?php
namespace ikarus\system\option;
use ikarus\system\IKARUS;
use ikarus\system\io\File;
use ikarus\system\option\type;

/**
 * Manages option files of applications
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class Options {

	/**
	 * Contains a prefix for option names
	 * @var string
	 */
	const OPTION_PREFIX = 'OPTION_';

	/**
	 * Contains a suffix for option names
	 * @var string
	 */
	const OPTION_SUFFIX = '';

	/**
	 * Generates the option file for given package
	 * @param	string	$file
	 * @param	integer	$packageID
	 */
	public static function generate($file, $packageID = PACKAGE_ID) {
		// delete existing files
		if (file_exists($file)) @unlink($file);

		// get options
		$sql = "SELECT
				systemOption.optionName AS optionName,
				systemOption.optionValue AS optionValue,
				systemOption.optionType AS optionType,
				systemOption.prependPrefix AS prependPrefix,
				systemOption.appendSuffix AS appendSuffix,
				CONCAT(package.packagePath, optionType.classFile) AS classFile
			FROM
				ikarus".IKARUS_N."_option systemOption
			LEFT JOIN
				ikarus".IKARUS_N."_package_dependency dependency
			ON
				systemOption.packageID = dependency.dependencyID
			LEFT JOIN
				ikarus".IKARUS_N."_option_type optionType
			ON
				(systemOption.optionType = optionType.typeName)
			LEFT JOIN
				ikarus".IKARUS_N."_package package
			ON
				(optionType.packageID = package.packageID)
			WHERE
				dependency.packageID = ".$packageID."
			OR
				systemOption.packageID = ".$packageID;
		$result = IKARUS::getDatabase()->sendQuery($sql);

		// create file
		$file = new File($file);
		$file->write("<?php\nnamespace \;\n/**\n * Ikarus Option File\n * Generated on ".gmdate('r')."\n **/\n\n");

		while($row = IKARUS::getDB()->fetchArray($result)) {
			require_once(IKARUS_DIR.$row['classFile']);
			
			$className = 'ikarus\\system\\option\\type\\'.basename($row['classFile'], '.class.php');
			$file->write("if (!defined('".(intval($row['prependPrefix']) ? self::OPTION_PREFIX : '').strtoupper($row['optionName']).(intval($row['appendSuffix']) ? self::OPTION_SUFFIX : '')."')) define('".(intval($row['prependPrefix']) ? self::OPTION_PREFIX : '').strtoupper($row['optionName']).(intval($row['appendSuffix']) ? self::OPTION_SUFFIX : '')."', ".call_user_func(array($className, 'formatOptionValue'), $row['optionValue']).");\n");
		}

		$file->write("\n/** EOF **/\n?>");
	}
}
?>