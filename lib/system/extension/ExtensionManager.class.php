<?php
namespace ikarus\system\extension;
use ikarus\system\Ikarus;

/**
 * Provides hook methods for extensions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ExtensionManager {
	
	/**
	 * Contains a list of callbacks that should be executed before flushing output buffer
	 * @var			array<callable>
	 */
	protected $outputHooks = array();
	
	/**
	 * Contains a list of callbacks that should be executed before stopping the application
	 * @var			array<callable>
	 */
	protected $shutdownHooks = array();
	
	/**
	 * Creates a new instance of type ExtensionManager
	 */
	public function __construct() {
		ob_start(array($this, 'handleOutputBuffer'));
	}
	
	/**
	 * Registers a new output buffer hook
	 * @param			callable			$callback
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function addOutputHook($callback) {
		// validate callback
		if (!is_callable($callback)) throw new StrictStandardException('The given parameter is not a valid callback');
		
		// save callback
		$this->outputHooks[] = $callback;
	}
	
	/**
	 * Registers a new shutdown hook
	 * @param			callable			$callback
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function addShutdownHook($callback) {
		// validate callback
		if (!is_callable($callback)) throw new StrictStandardException('The given parameter is not a valid callback');
		
		// save callback
		$this->shutdownHooks[] = $callback;
	}
	
	/**
	 * Handles the output buffer before flush is executed
	 * @param			string			$buffer
	 * @return			string
	 */
	public function handleOutputBuffer($buffer) {
		foreach($this->outputHooks as $hook) {
			$buffer = call_user_func($hook, $buffer);
		}
		
		if (Ikarus::getConfiguration()->get('global.http.enableGzip')) $buffer = ob_gzhandler($buffer, Ikarus::getConfiguration()->get('global.http.gzipMode'));
		
		return $buffer;
	}
	
	/**
	 * Calls all registered shutdown hooks
	 * @return			void
	 */
	public function shutdown() {
		foreach($this->shutdownHooks as $hook) {
			call_user_func($hook);
		}
	}
}
?>