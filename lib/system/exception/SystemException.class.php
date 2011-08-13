<?php
namespace ikarus\system\exception;
use \Exception;
use ikarus\system\IKARUS;
use ikarus\system\exception\PrintableException;
use ikarus\util\FileUtil;
use ikarus\util\StringUtil;

/**
 * This exception will thrown if an system error occours
 * Note: This is the red screen of death
 * @author		Johannes Donath, Some code taken from WoltLab Community Framework (developed by Marcel Werk)
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class SystemException extends Exception implements IPrintableException {

	/**
	 * Contains additional error information
	 * @var	string
	 */
	protected $information = '';

	/**
	 * Contains additional html code for error
	 * @var	string
	 */
	protected $functions = '';

	/**
	 * Creates a new instance of SystemException (The message argument has the same syntax as printf())
	 * @param	string	$message
	 * @param	string	$argument1
	 * @param	string	$argument2
	 * ...
	 */
	public function __construct() {
		// validate arguments
		if (!func_num_args()) die("<strong>FATAL:</strong> Cannot display SystemException: Invalid arguments passed to system exception!");

		// get arguments (sorry for this shit but i would like a c like system exception ;-D)
		$arguments = func_get_args();

		// remove argument1 (message)
		$message = $arguments[0];

		// get error code
		$code = 0;
		for($i = 0; $i < strlen($message); $i++) {
			$code = $code + ($i + 1) + ord($message{$i});
		}
		$code = $code * 100;
		
		// little workaround
		// Note this is absolutly senseless ... xD
		if (isset($arguments[1]) && is_array($arguments[1])) {
			$fixedArguments = array(0 => $message);
			foreach($arguments[1] as $val) {
				$fixedArguments[] = $val;
			}
			$arguments = $fixedArguments;
		}
		
		// format new message
		if (count($arguments) > 1) $message = call_user_func_array('sprintf', $arguments);

		// call Exception::__construct()
		parent::__construct($message, $code);
	}

	/**
	 * Removes database password from stack trace.
	 * @see			Exception::getTraceAsString()
	 * @author		Marcel Werk
	 * @copyright		2001-2009 WoltLab GmbH
	 */
	public function __getTraceAsString() {
		$string = preg_replace('/Database->__construct\(.*\)/', 'Database->__construct(...)', $this->getTraceAsString());
		$string = preg_replace('/mysqli->mysqli\(.*\)/', 'mysqli->mysqli(...)', $string);
		$string = preg_replace('/Database->connect\(.*\)/', 'MySQLDatabase->connect(...)', $string);
		$string = preg_replace('/(my|)sql_connect\(.*\)/', '$1sql_connect(...)', $string);
		$string = preg_replace('/DatabaseManager->addConnection\(.*\)/', 'DatabaseManager->addConnection(...)', $string);
		return $string;
	}

	/**
	 * @see	PrintableException::show()
	 * @author		Marcel Werk, Some little modifications by Johannes Donath
	 * @copyright		2001-2009 WoltLab GmbH
	 */
	public function show() {
		// send status code
		@header('HTTP/1.1 503 Service Unavailable');

		// notify application manager
		IKARUS::getApplicationManager()->handleApplicationError($this);
	}
}
?>