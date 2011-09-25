<?php
namespace ikarus;

/**
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
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

// register shutdown method
register_shutdown_function(array('ikarus\system\Ikarus', 'shutdown'));

// assert settings
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, array('ikarus\system\Ikarus', 'handleAssertion'));

/**
 * Escapes a string with the correct method for the current database connection
 * @param		string			$string
 * @return 		string
 */
function escapeString($string) {
	return ikarus\system\Ikarus::getDatabaseManager()->escapeString($string);
}
?>