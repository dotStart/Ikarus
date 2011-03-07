<?php

/**
 * This exception is senseless
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class NamedUserException extends UserException {

	/**
	 * @see UserException::$templateName
	 */
	public $templateName = 'namedUserException';

	/**
	 * @see UserException::$header
	 */
	public $header = 'HTTP/1.1 404 418 I\'m a Teapot';
}
?>