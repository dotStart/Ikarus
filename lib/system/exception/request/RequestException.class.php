<?php
namespace ikarus\system\exception\request;
use ikarus\system\exception\ApplicationException;

/**
 * The base class for all request related problems.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class RequestException extends NamedUserException { }
?>