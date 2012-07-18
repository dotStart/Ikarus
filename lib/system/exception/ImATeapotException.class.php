<?php
namespace ikarus\system\exception;

/**
 * This exception will be thrown if someone tries to fill coffee in this teapot.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class IAmATeapotException extends NamedUserException {
	
	/**
	 * @see ikarus\system\exception.NamedUserException::$header
	 */
	protected $header = 'HTTP/1.1 418 I\'m a teapot';
}
?>