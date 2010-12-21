<?php

/**
 * This exception will thrown if an system error occours
 * Note: This is the red screen of death
 * @author		Johannes Donath, Some code taken from WoltLab Community Framework (developed by Marcel Werk)
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class SystemException extends Exception implements PrintableException {

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

		// resort array
		$arguments = array_merge(array(), $arguments);

		// get error code
		$code = 0;
		for($i = 0; $i < strlen($message); $i++) {
			$code = $code + ($i + 1) + ord($message{$i});
		}
		$code = $code * 100;

		// format new message
		$message = call_user_func_array('sprintf', $arguments);

		// call Exception::__construct()
		parent::__construct($message, $code);
	}

	/**
	 * Removes database password from stack trace.
	 * @see			Exception::getTraceAsString()
	 * @author		Marcel Werk
	 * @copyright	2001-2009 WoltLab GmbH
	 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
	 * @package		com.woltlab.wcf
	 * @subpackage	system.exception
	 * @category	Community Framework
	 */
	public function __getTraceAsString() {
		$string = preg_replace('/Database->__construct\(.*\)/', 'Database->__construct(...)', $this->getTraceAsString());
		$string = preg_replace('/mysqli->mysqli\(.*\)/', 'mysqli->mysqli(...)', $string);
		$string = preg_replace('/Database->connect\(.*\)/', 'MySQLDatabase->connect(...)', $string);
		$string = preg_replace('/(my|)sql_connect\(.*\)/', '$1sql_connect(...)', $string);
		$string = preg_replace('/DatabaseManager->addConnection\(.*\)/', 'DatabaseManager->addConnection(...)');
		return $string;
	}

	/**
	 * @see	PrintableException::show()
	 * @author		Marcel Werk, Some little modifications by Johannes Donath
	 * @copyright	2001-2009 WoltLab GmbH
	 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
	 * @package		com.woltlab.wcf
	 * @subpackage	system.exception
	 * @category	Community Framework
	 */
	public function show() {
		// send status code
		@header('HTTP/1.1 503 Service Unavailable');

		// print report
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en">
<head>
<title>Fatal error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></title>
<style type="text/css">
/*<![CDATA[*/
.systemException {
	border: 1px outset lightgrey;
	padding: 3px;
	background-color: lightgrey;
	text-align: left;
	overflow: auto;
	font-family: Verdana, Helvetica, sans-serif;
	font-size: .8em;
}
.systemException div {
	border: 1px inset lightgrey;
	padding: 4px;
}
.systemException h1 {
	background-color: #a92222;
	padding: 4px;
	color: #fff;
	margin: 0 0 3px 0;
	font-size: 1.15em;
}
.systemException h2 {
	font-size: 1.1em;
	margin-bottom: 0;
}
.systemException pre, .systemException p {
	margin: 0;
}
.systemException pre {
	font-size: .85em;
	font-family: "Courier New";
}

.systemException a {
	color: #a92222;
}

.systemException a:hover {
	color: #a93b3b;
	text-decoration: none;
}
/*]]>*/
</style>
</head>
<body>
	<div class="systemException">
		<h1>Fatal error: <?php echo StringUtil::encodeHTML($this->getMessage()); ?></h1>

		<div>
			<?php if ($this->getCode()) { ?><p>You get more information about the problem in our knowledge base: <a href="http://cp.develfusion.com/help/?code=<?php echo intval($this->getCode()); ?>">http://cp.develfusion.com/help/?code=<?php echo intval($this->getCode()); ?></a></p><?php } ?>

			<h2>Information:</h2>
			<p>
				<b>error message:</b> <?php echo StringUtil::encodeHTML($this->getMessage()); ?><br />
				<b>error code:</b> <?php echo intval($this->getCode()); ?><br />
				<?php echo $this->information; ?>
				<b>file:</b> <?php echo StringUtil::encodeHTML($this->getFile()); ?> (<?php echo $this->getLine(); ?>)<br />
				<b>php version:</b> <?php echo StringUtil::encodeHTML(phpversion()); ?><br />
				<b>cp version:</b> <?php echo CP_VERSION; ?><br />
				<b>date:</b> <?php echo gmdate('r'); ?><br />
				<b>request:</b> <?php if (isset($_SERVER['REQUEST_URI'])) echo StringUtil::encodeHTML($_SERVER['REQUEST_URI']); ?><br />
				<b>referer:</b> <?php if (isset($_SERVER['HTTP_REFERER'])) echo StringUtil::encodeHTML($_SERVER['HTTP_REFERER']); ?><br />
			</p>

			<h2>Stacktrace:</h2>
			<pre><?php echo StringUtil::encodeHTML($this->__getTraceAsString()); ?></pre>
		</div>

		<?php echo $this->functions; ?>
	</div>
</body>
</html>

<?php
	}
}
?>