<?php

/**
 * This exception displays a little "Page Not Found" message
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class IllegalLinkException extends UserException {
	
	/**
	 * @see UserException::$templateName
	 */
	public $templateName = 'illegalLinkException';
}
?>