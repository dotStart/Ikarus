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
namespace ikarus\system\request;
use ikarus\pattern\Singleton;
use ikarus\system\application\IApplication;
use ikarus\system\event\request\ControllerEventArguments;
use ikarus\system\event\request\ExecuteControllerEvent;
use ikarus\system\event\request\DispatcherEventArguments;
use ikarus\system\event\request\DispatcherFailedEvent;
use ikarus\system\event\request\DispatchEvent;
use ikarus\system\exception\request\IllegalLinkException;
use ikarus\system\Ikarus;
use ikarus\util\ArrayUtil;
use ikarus\util\ClassUtil;
use ikarus\util\FileUtil;

/**
 * Manages routes and dispatches them to correct controller
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class RequestDispatcher extends Singleton {

	/**
	 * Contains a list of available controller types
	 * @var			array<array<string>>
	 */
	protected $availableControllerTypes = array();

	/**
	 * Contains a list of available routes
	 * @var			array<string>
	 */
	protected $availableRoutes = array();

	/**
	 * @see ikarus\pattern.Singleton::__construct()
	 */
	protected function __construct($packageID = IKARUS_N) {
		parent::__construct();

		$this->loadDispatcherCache($packageID);
	}

	/**
	 * Dispatches a request to correct controller
	 * @param			IApplication		$application
	 * @param			string			$requestParameters
	 * @throws IllegalLinkException
	 */
	public function dispatch(IApplication $application, $requestParameters) {
		// set defaults
		// FIXME: This should not be hardcoded
		if (!ArrayUtil::in_array(array_keys($requestParameters), $this->availableControllerTypes)) $requestParameters['page'] = 'Index';

		// fire event
		$event = new DispatchEvent(new DispatcherEventArguments($application, $requestParameters));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

		// search for routes
		// Note: This allows shadowing existing static routes (Defined by files)
		foreach($this->availableRoutes as $routeParameter => $routes)
			if (isset($requestParameters[$routeParameter]))
				foreach($routes as $routeName => $executionInformation)
					if ($routeName == $requestParameters[$routeParameter] and $this->loadController($executionInformation['controllerName'], $executionInformation['controllerNamespace'], $application)) return $this->executeController($executionInformation['controllerName'], $executionInformation['controllerNamespace'], $application);

		// find controller types
		foreach($this->availableControllerTypes as $name => $controllerNamespace)
			if (isset($requestParameters[$name]) and $this->loadController($requestParameters[$name].ucfirst($name), $controllerNamespace, $application)) return $this->executeController($requestParameters['name'], $controllerNamespace, $application);

		// fire event
		$event = new DispatcherFailedEvent(new DispatcherEventArguments($application, $requestParameters));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

		// no routes found
		throw new IllegalLinkException('There are no routes and no controllers available for this request.');
	}

	/**
	 * Executes a controller.
	 * @param			string			$controllerName
	 * @param			string			$controllerNamespace
	 * @param			IApplication		$application
	 */
	public function executeController($controllerName, $controllerNamespace, $application) {
		// build classPath
		$classPath = ClassUtil::buildPath($application->getLibraryNamespace(), $controllerNamespace, $controllerName);

		// create controller instance
		$controller = new $classPath();

		// fire event
		$event = new ExecuteControllerEvent(new ControllerEventArguments($controller));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled() and $event->getReplacement() === null)
			throw new StrictStandardException('A replacement for controller execution is missing');
		elseif ($event->isCancelled())
			$controller = $event->getReplacement();

		// execute controller
		$controller->init();
	}

	/**
	 * Loads a controller.
	 * @param		string			$controllerName
	 * @param		string			$controllerNamespace
	 * @param		IApplication		$application
	 */
	public function loadController($controllerName, $controllerNamespace, $application) {
		// build classPath
		$classPath = ClassUtil::buildPath($application->getLibraryNamespace(), $controllerNamespace, $controllerName);

		// try to load controller
		return class_exists($classPath, true);
	}

	/**
	 * Loads all available dispatcher caches
	 * @return			void
	 */
	protected function loadDispatcherCache($packageID) {
		// add resources
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('requestDispatcherControllerTypes-'.$packageID, 'requestDispatcherControllerTypes-'.$packageID, 'ikarus\system\cache\builder\CacheBuilderRequestDispatcherControllerTypes');
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('requestDispatcherAvailableRoutes-'.$packageID, 'requestDispatcherAvailableRoutes-'.$packageID, 'ikarus\system\cache\builder\CacheBuilderRequestDispatcherAvailableRoutes');

		// save information
		$this->availableControllerTypes = Ikarus::getCacheManager()->getDefaultAdapter()->get('requestDispatcherControllerTypes-'.$packageID);
		$this->availableRoutes = Ikarus::getCacheManager()->getDefaultAdapter()->get('requestDispatcherAvailableRoutes-'.$packageID);
	}
}
?>