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

// register autoloader
spl_autoload_register(array('IKARUS', 'autoload'));

/**
 * Escapes a string with the correct method for the current database connection
 * @param	string	$string
 * @return string
 */
function escapeString($string) {
	return IKARUS::getDatabase()->escapeString($string);
}
?>