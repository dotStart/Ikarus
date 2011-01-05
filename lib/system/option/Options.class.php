<?php

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
	 * Contains a suffix for option type names
	 * @var string
	 */
	const OPTION_TYPE_SUFFIX = 'OptionType';

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
					CONCAT(package.packagePath, optionType.classFile) AS classFile
				FROM
					ikarus".IKARUS_N."_option systemOption
				JOIN
					ikarus".IKARUS_N."_option_type optionType
				ON
					(systemOption.optionType = optionType.typeName)
				JOIN
					ikarus".IKARUS_N."_package package
				ON
					(optionType.packageID = package.packageID)
				WHERE
					systemOption.packageID = ".$packageID;
		$result = IKARUS::getDatabase()->sendQuery($sql);

		// create file
		$file = new File($file);
		$file->write("<?php\n/**\n * Ikarus Option File\n * Generated on ".gmdate('r')."\n **/\n\n");

		while($row = IKARUS::getDB()->fetchArray($result)) {
			require_once($row['classFile']);
			$file->write("define('".self::OPTION_PREFIX.StringUtil::toUpperCase($row['optionName']).self::OPTION_SUFFIX."', ".call_user_func(array($row['optionType'].self::OPTION_TYPE_SUFFIX, 'formatOptionValue'), $row['optionValue']).");\n");
		}

		$file->write("\n/** EOF **/\n?>");
	}
}
?>