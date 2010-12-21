<?php
/**
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */

// set exception handler
set_exception_handler(array('IKARUS', 'handleException'));

// set error handler
set_error_handler(array('IKARUS', 'handleError'), E_ALL);

/**
 * Loads missing class definitions from framework dir
 * Supported dirs are:
 * 		lib/system/exception
 * 		lib/util/
 * Note: This will search in all available package dirs include Ikarus
 * @param	string	$className
 */
function __autoload($className) {
	foreach($packageDirs as $dir) {
		// search exceptions
		if (file_exists($dir.'lib/system/exception/'.$className.'.class.php')) {
			require_once($dir.'lib/system/exception/'.$className.'.class.php');
			return;
		}

		// search utils
		if (file_exists($dir.'lib/util/'.$className.'.class.php')) {
			require_once($dir.'lib/util/'.$className.'.class.php');
		}
	}
}

/**
 * Escapes a string with the correct method for the current database connection
 * @param	string	$string
 * @return string
 */
function escapeString($string) {
	return IKARUS::getDatabase()->escapeString($string);
}
?>