<?php
namespace ikarus\system\exception;

/**
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ApplicationException extends SystemException {
	
	/**
	 * @see ikarus\system\exception.SystemException::EXCEPTION_TITLE
	 */
	const EXCEPTION_TITLE = 'Application error';
}
?>