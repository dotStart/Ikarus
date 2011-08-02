<?php
namespace ikarus\system\exception;
use \Exception;
use ikarus\system\IKARUS;

/**
 * Abstract exception that displays predefined templates
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class UserException extends Exception implements PrintableException {

	/**
	 * Contains the name of the template that should displayed
	 * @var string
	 */
	public $templateName = '';

	/**
	 * Contains a header that should appear
	 * @var string
	 */
	public $header = '';

	/**
	 * @see PrintableException::show()
	 */
	public function show() {
		// send headers
		if (!empty($this->header)) @header($this->header);

		// display template
		try {
			if (!empty($this->templateName)) IKARUS::getTemplate()->display($this->templateName);
		} Catch(Exception $ex) { echo $ex->getMessage(); echo $ex->getTraceAsString(); }
	}
}
?>