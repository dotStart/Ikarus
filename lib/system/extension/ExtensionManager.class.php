<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
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
	 * Contains a list of callbacks that should be executed after autoloading a missing class
	 * @var			array<callable>
	 */
	protected $autoloadHooks = array();
	
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
	 * Registers a new autoload hook
	 * @param			callable			$callback
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function addAutoloadHook($callback) {
		// validate callback
		if (!is_callable($callback)) throw new StrictStandardException('The given parameter is not a valid callback');
		
		// save callback
		$this->autoloadHooks[] = $callback;
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
	 * Executes all autoload hooks
	 * @param			string			$className
	 * @return			void
	 */
	public function autoload($className) {
		foreach($this->autoloadHooks as $hook) {
			call_user_func($hook, $className);
		}
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