<?php
/**
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */

// set exception handler
set_exception_handler(array('IKARUS', 'handleException'));

// set error handler
set_error_handler(array('IKARUS', 'handleError'), E_ALL);

// register shutdown method
register_shutdown_function(array('IKARUS', 'destruct'));

/**
 * Autoloads classes in namespaces
 * 
 * @param		string		$className
 */
function autoload($className) {
	// get globals
	global $packageList;
	
	// little validation ... (this should never happen)
	if (empty($className)) return;
	
	// split to parts
	$namespaceParts = explode('\\', $className);
	
	// find prefix
	foreach($packageList as $packagePrefix => $packageDir) {
		if ($namespaceParts[0] == $packagePrefix) {
			// remove prefix
			$className = substr($className, (strlen($packagePrefix + 1)));
			
			// replace backslashes
			$className = str_replace('\\', '/', $className);
			
			// add '.class.php'
			$className .= '.class.php';
			
			// try to find file and include
			if (file_exists($packageDir.$className)) require_once($packageDir.$className);
			
			// exit the autoloader here. We've unique prefixes.
			return;
		}
	}
}
spl_autoload_register('autoload');

/**
 * Escapes a string with the correct method for the current database connection
 * @param	string	$string
 * @return string
 */
function escapeString($string) {
	return IKARUS::getDatabase()->escapeString($string);
}
?>