<?php
namespace ikarus\system\exception\request;

/**
 * This exception will be thrown if a method (like GET) is not allowed on a resource.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class MethodNotAllowedException extends AccessDeniedException {
	
	/**
	 * @see ikarus\system\exception.NamedUserException::$header
	 */
	protected $header = 'HTTP/1.1 405 Method Not Allowed';
}
?>