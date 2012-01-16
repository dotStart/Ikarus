<?php
namespace ikarus\system\exception;
use ikarus\system\Ikarus;

/**
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class NamedUserException extends SystemException {
	
	/**
	 * @see ikarus\system\exception.SystemException::EXCEPTION_TITLE
	 */
	const EXCEPTION_TITLE = 'Illegal link';
	
	/**
	 * Contains a HTTP header that should be used
	 * @var			string
	 */
	protected $header = 'HTTP/1.1 200 Ok';
	
	/**
	 * Contains the name of a template that should appear
	 * @var			string
	 */
	protected $template = 'namedUserException';
	
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
		// template system
		if (Ikarus::componentLoaded('ikarus\system\template\Template') and !empty($this->template)) {
			Ikarus::getTemplate()->assign('message', $this->message);
			Ikarus::getTemplate()->display($this->template);
			return;
		}
		
		// no template system
		parent::showMinimal();
	}
}
?>