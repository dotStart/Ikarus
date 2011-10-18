<?php
namespace ikarus\system\exception;
use \Exception;

/**
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class HiddenApplicationException extends SystemException {
	
	/**
	 * @see Exception::__construct()
	 */
	public function __construct($message = "", $code = 0, SystemException $previous = null) {
		Exception::__construct($message, $code, $previous);
	}
	
	/**
	 * @see ikarus\system\exception.SystemException::show()
	 */
	public function show() {
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<title>An error occoured</title>
				<link rel="stylesheet" type="text/css" href="<?php echo RELATIVE_IKARUS_DIR; ?>style/fatalError.css" />
				<script type="text/javascript" src="<?php echo RELATIVE_IKARUS_DIR; ?>js/3rdParty/jquery.min.js"></script>
				<script type="text/javascript" src="<?php echo RELATIVE_IKARUS_DIR; ?>js/3rdParty/jquery-ui.min.js"></script>
			</head>
			<body>
				<div class="hiddenApplicationException">
					<h1>An error occoured</h1>
					<p>Sorry but an error occoured while generating the content for this page. Please contact an administrator if this content persists. If you are the administrator of this web page you can enable error messages in your administration panel or search the log files.</p>
					
					<?php
					$errorReport = $this->getPrevious()->generateErrorReport();
					
					if (preg_match('~ENCRYPTED REPORT~i', $errorReport)) {
					?>
					<h2><a href="javascript:void(0);" onclick="$('#errorReport').toggle('blind'); $(this).text(($(this).text() == '+' ? '-' : '+'));">+</a> Support Information</h2>
					<div id="errorReport" style="display: none;">
						<p>If you are the administrator of this page you can use the following information for supporting purposes:</p>
						<pre><?php echo $errorReport; ?></pre>
					</div>
					<?php
					}
					?>
				</div>
			</body>
		</html>
		<?php
	}
}
?>