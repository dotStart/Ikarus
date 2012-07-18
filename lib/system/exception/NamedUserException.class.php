<?php
namespace ikarus\system\exception;
use ikarus\system\Ikarus;

/**
 * The base class for all user specific problems.
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class NamedUserException extends SystemException {
	
	/**
	 * @see ikarus\system\exception.SystemException::EXCEPTION_TITLE
	 */
	const EXCEPTION_TITLE = 'Unknown problem';
	
	/**
	 * Contains a HTTP header that should be used
	 * @var			string
	 */
	protected $header = 'HTTP/1.1 400 Bad Request';
	
	/**
	 * Creates a new instance of type SystemException
	 * @param			string			$message
	 */
	public function __construct($message = '') {
		$this->message = $message;
	}
	
	/**
	 * @see ikarus\system\exception.SystemException::show()
	 */
	public function show() {
		parent::showMinimal();
	}
}
?>