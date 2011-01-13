<?php

/**
 * Abstract exception that displays predefined templates
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
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
		if (!empty($this->templateName)) @header($this->header);
		
		// display template
		if (!empty($this->header)) IKARUS::getTemplate()->display($this->templateName);
	}
}
?>