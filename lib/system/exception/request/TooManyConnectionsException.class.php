<?php
namespace ikarus\system\exception\request;

/**
 * This exception will be thrown if there are too many connections from a client.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class TooManyConnectionsException extends AccessDeniedException {
	
	/**
	 * @see ikarus\system\exception.NamedUserException::$header
	 */
	protected $header = 'HTTP/1.1 421 There are too many connections from your internet address';
}
?>