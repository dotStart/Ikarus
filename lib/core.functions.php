<?php
namespace ikarus;

/**
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus\system\Ikarus
 * @subpackage		system
 * @category		ikarus\system\Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */

// set exception handler
set_exception_handler(array('ikarus\system\Ikarus', 'handleException'));

// set error handler
set_error_handler(array('ikarus\system\Ikarus', 'handleError'), E_ALL);

// register autoloader
spl_autoload_register(array('ikarus\system\Ikarus', 'autoload'));

/**
 * Escapes a string with the correct method for the current database connection
 * @param		string			$string
 * @return 		string
 */
function escapeString($string) {
	return ikarus\system\Ikarus::getDatabaseManager()->escapeString($string);
}
?>