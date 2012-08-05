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
use ikarus\system\event\CallbackEventArguments;
use ikarus\system\event\ClassNameEventArguments;
use ikarus\system\event\EmptyEventArguments;
use ikarus\system\event\extension\AddAutoloadHookEvent;
use ikarus\system\event\extension\AddOutputHookEvent;
use ikarus\system\event\extension\AddShutdownHookEvent;
use ikarus\system\event\extension\AutoloadEvent;
use ikarus\system\event\extension\AutoloadHookExecuteEvent;
use ikarus\system\event\extension\AutoloadHooksExecutedEvent;
use ikarus\system\event\extension\HandleOutputBufferEvent;
use ikarus\system\event\extension\OutputHookExecuteEvent;
use ikarus\system\event\extension\OutputGzipEnabledEvent;
use ikarus\system\event\extension\OutputGzippedEvent;
use ikarus\system\event\extension\OutputHandlerExecutedEvent;
use ikarus\system\event\extension\ShutdownEvent;
use ikarus\system\event\extension\ShutdownHookExecuteEvent;
use ikarus\system\event\extension\ShutdownHooksExecutedEvent;
use ikarus\system\event\io\BufferEventArguments;
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

		// fire event
		$event = new AddAutoloadHookEvent(new CallbackEventArguments($callback));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

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

		// fire event
		$event = AddOutputHookEvent(new CallbackEventArguments($callback));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

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

		// fire event
		$event = AddShutdownHookEvent(new CallbackEventArguments($callback));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

		// save callback
		$this->shutdownHooks[] = $callback;
	}

	/**
	 * Executes all autoload hooks
	 * @param			string			$className
	 * @return			void
	 */
	public function autoload($className) {
		// fire event
		$event = new AutoloadEvent(new ClassNameEventArguments($className));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

		// loop through autoload hooks
		foreach($this->autoloadHooks as $hook) {
			// fire event
			$event = new AutoloadHookExecuteEvent(new CallbackEventArguments($hook));
			Ikarus::getEventManager()->fire($event);

			// cancellable event
			if ($event->isCancelled()) continue;

			// execute hook
			call_user_func($hook, $className);
		}

		// fire event
		Ikarus::getEventManager()->fire(new AutoloadHooksExecutedEvent(new ClassNameEventArguments($className)));
	}

	/**
	 * Handles the output buffer before flush is executed
	 * @param			string			$buffer
	 * @return			string
	 */
	public function handleOutputBuffer($buffer) {
		// fire event
		$event = new HandleOutputBufferEvent(new BufferEventArguments($buffer));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new StrictStandardException("Missing replacement for output buffer");
		elseif ($event->isCancelled())
			return $event->getReplacement();

		// loop through output hooks
		foreach($this->outputHooks as $hook) {
			// fire event
			$event = new OutputHookExecuteEvent(new CallbackEventArguments($hook));
			Ikarus::getEventManager()->fire($event);

			// cancellable event
			if ($event->isCancelled()) continue;

			// execute hook
			$buffer = call_user_func($hook, $buffer);
		}

		// gzip enabled
		if (Ikarus::getConfiguration()->get('global.http.enableGzip')) {
			// fire event
			$event = new OutputGzipEnabledEvent(new BufferEventArguments($buffer));
			Ikarus::getEventManager()->fire($event);

			// cancellable event
			if (!$event->isCancelled()) {
				// gzip buffer
				$buffer = ob_gzhandler($buffer, Ikarus::getConfiguration()->get('global.http.gzipMode'));

				// fire event
				$event = new OutputGzippedEvent(new BufferEventArguments($buffer));
				Ikarus::getEventManager()->fire($event);

				// cancellable event
				if ($event->isCancelled() and $event->getReplacement() === null)
					throw new StrictStandardException("Missing replacement for gzipped output buffer");
				elseif ($event->isCancelled())
					$buffer = $event->getReplacement();
			}
		}

		// fire event
		$event = new OutputHandlerExecutedEvent(new BufferEventArguments($buffer));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new StrictStandardException("Missing replacement for output buffer");
		elseif ($event->isCancelled())
			$buffer = $event->getReplacement();

		// return buffer
		return $buffer;
	}

	/**
	 * Calls all registered shutdown hooks
	 * @return			void
	 */
	public function shutdown() {
		// fire event
		Ikarus::getEventManager()->fire(new ShutdownEvent(new EmptyEventArguments()));

		// loop through shutdown hooks
		foreach($this->shutdownHooks as $hook) {
			// fire event
			$event = new ShutdownHookExecuteEvent(new CallbackEventArguments($hook));
			Ikarus::getEventManager()->fire($event);

			// cancellable event
			if ($event->isCancelled()) continue;

			// execute hook
			call_user_func($hook);
		}

		// fire event
		Ikarus::getEventManager()->fire(new ShutdownHooksExecutedEvent(new EmptyEventArguments()));
	}
}
?>