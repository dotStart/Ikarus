<?php
namespace ikarus\system\exception;
use \Exception;
use ikarus\system\Ikarus;
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
	protected $information = array();

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
	 * Removes full paths from issuer file
	 * @return		string
	 */
	public function __getFile() {
		return FileUtil::removeTrailingSlash(FileUtil::getRelativePath(IKARUS_DIR, $this->getFile()));
	}

	/**
	 * Removes database password from stack trace.
	 * @see			Exception::getTraceAsString()
	 * @author		Marcel Werk
	 * @copyright		2001-2009 WoltLab GmbH
	 */
	public function __getTraceAsString() {
		// get trace array
		$trace = $this->getTrace();
		
		// init variables
		$string = "";
		
		// add elements
		foreach($trace as $index => $element) {
			$string .= "#".$index." ".$this->prepareFilePath($element['file'])."(".$element['line']."): ".(isset($element['class']) ? $element['class'].$element['type'] : '').$element['function'].'(';
			foreach($element['args'] as $key => $argument) {
				if ($key < 0) $string .= ' ,';
				$string .= gettype($argument);
				
				switch(gettype($argument)) {
					case 'array':
						$string .= '('.count($argument).')';
						break;
					case 'boolean':
						$string .= '('.($argument ? 'true' : 'false');
						break;
					case 'integer':
					case 'float':
					case 'double':
						$string .= '('.$argument.')';
						break;
					case 'object':
						if (function_exists('spl_object_hash')) $string .= '('.spl_object_hash($argument).')';
						break;
					case 'string':
						$string .= '('.strlen($argument).')';
						break;
				}
			}
			$string .= ")\n";
		}
		
		
		$string = preg_replace('/Database->__construct\(.*\)/', 'Database->__construct(...)', $string);
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
		Ikarus::getApplicationManager()->handleApplicationError($this);
	}
	
	public function showMinimal() {
		// send status code
		@header('HTTP/1.1 503 Service Unavailable');
		
		// print report
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<title>Fatal error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></title>
				<link rel="stylesheet" type="text/css" href="<?php echo RELATIVE_IKARUS_DIR; ?>style/fatalError.css" />
				<script type="text/javascript" src="<?php echo RELATIVE_IKARUS_DIR; ?>js/3rdParty/jquery.min.js"></script>
				<script type="text/javascript" src="<?php echo RELATIVE_IKARUS_DIR; ?>js/3rdParty/jquery-ui.min.js"></script>
			</head>
			<body>
				<div class="systemException">
					<h1>Core error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></h1>
					
					<div>
						<p>
							<b>error message:</b> <?php echo StringUtil::encodeHTML($this->getMessage()); ?><br />
							<b>error code:</b> <a href="http://www.ikarus-framework.de/error/<?php echo intval($this->getCode()); ?>"><?php echo intval($this->getCode()); ?></a><br />
							<?php 
							foreach($this->information as $label => $value) echo '<b>'.$label.':</b> '.$value.'<br />';
							?>
							<b>file:</b> <?php echo StringUtil::encodeHTML($this->__getFile()); ?> (<?php echo $this->getLine(); ?>)<br />
							<b>php version:</b> <?php echo StringUtil::encodeHTML(phpversion()); ?> (<?php echo PHP_OS; ?>)<br />
							<b>ikarus version:</b> <?php echo IKARUS_VERSION; ?><br />
							<b>memory:</b> <?php echo memory_get_peak_usage(); ?> bytes<br />
							<b>date:</b> <?php echo gmdate('r'); ?><br />
							<b>request:</b> <?php if (isset($_SERVER['REQUEST_URI'])) echo StringUtil::encodeHTML($_SERVER['REQUEST_URI']); ?><br />
							<b>referer:</b> <?php if (isset($_SERVER['HTTP_REFERER'])) echo StringUtil::encodeHTML($_SERVER['HTTP_REFERER']); ?><br />
						</p>
						
						<h2><a href="javascript:void(0);" onclick="$('#stacktrace').toggle('blind'); $(this).text(($(this).text() == '+' ? '-' : '+'));">+</a>Stacktrace</h2>
						<pre id="stacktrace" style="display: none;"><?php echo StringUtil::encodeHTML($this->__getTraceAsString()); ?></pre>
						
						<h2><a href="javascript:void(0);" onclick="$('#files').toggle('blind'); $(this).text(($(this).text() == '+' ? '-' : '+'));">+</a>Files</h2>
						<pre id="files" style="display: none;"><?php $includes = array_map(array($this, 'prepareFilePath'), get_included_files()); asort($includes); foreach($includes as $file) echo $file."\n"; ?></pre>

						<h2><a href="javascript:void(0);" onclick="$('#definedConstants').toggle('blind'); $(this).text(($(this).text() == '+' ? '-' : '+'));">+</a>Constants</h2>
						<pre id="definedConstants" style="display: none;"><?php $constants = get_defined_constants(true); $constants = array_keys($constants['user']); asort($constants); foreach($constants as $constant) echo $constant."\n"; ?></pre>
					</div>
					
					<?php echo $this->functions; ?>
				</div>
			</body>
		</html>
		<?php
	}
	
	/**
	 * Calculates the relative path to given file
	 * @param			string		$path
	 * @return			string
	 */
	protected function prepareFilePath($path) {
		return FileUtil::removeTrailingSlash(FileUtil::getRelativePath(IKARUS_DIR, $path));	
	}
}
?>