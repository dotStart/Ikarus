<?php
namespace ikarus\system\request;
use ikarus\pattern\Singleton;
use ikarus\system\exception\IllegalLinkException;
use ikarus\system\Ikarus;

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
	 * @param			string			$application
	 * @param			string			$requestParameters
	 * @throws IllegalLinkException
	 */
	public function dispatch($application, $requestParameters) {
		// set defaults
		// FIXME: This should not be hardcoded
		if (!count(array_diff(array_keys($this->availableControllerTypes), array_keys($requestParameters)))) $requestParameters['page'] = 'Index';
		
		// find controller types
		foreach($this->availableControllerTypes as $name => $controllerDirectory)
			if (isset($requestParameters[$name]) and $this->loadController($requestParameters['name'], $controllerDirectory, $application)) return $this->executeController($requestParameters['name'], $controllerDirectory, $application);
		
		// search for routes
		foreach($this->availableRoutes as $routeParameter => $routes)
			if (isset($requestParameters[$routeParameter]))
				foreach($routes as $routeName => $executionInformation)
					if ($routeName == $requestParameters[$routeParameter] and $this->loadController($executionInformation['controllerName'], $executionInformation['controllerDirectory'], $application)) return $this->executeController($executionInformation['controllerName'], $executionInformation['controllerDirectory'], $application);
		
		throw new IllegalLinkException('There are no routes and no controllers available');
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