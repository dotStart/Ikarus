<?php
namespace ikarus\system\application;
use ikarus\system\Ikarus;

/**
 * Implements needed default methods for application cores
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class AbstractApplication implements IApplication {
	
	/**
	 * Contains the abbreviation for this application
	 * @var			string
	 */
	protected $abbreviation = '';
	
	/**
	 * Contains all application components
	 * @var			array
	 */
	protected $components = array();
	
	/**
	 * Contains an absolute library path for this application
	 * @var			string
	 */
	protected $libraryPath = '';
	
	/**
	 * Contains the packageID of this application
	 * @var			integer
	 */
	protected $packageID = 0;
	
	/**
	 * @see ikarus\system\application.IApplication::__construct()
	 */
	public function __construct($abbreviation, $libraryPath, $packageID) {
		$this->abbreviation = $abbreviation;
		$this->libraryPath = $libraryPath;
		$this->packageID = $packageID;
		
		Ikarus::getEventManager()->fire($this, 'init');
		
		$this->registerDefaultComponents();
		$this->registerDefaultCacheResources();
		
		Ikarus::getEventManager()->fire($this, 'initFinished');
	}
	
	/**
	 * @see ikarus\system\application.IApplication::addComponent()
	 */
	public function addComponent($componentName, $instance) {
		if ($this->componentExists($componentName)) throw new StrictStandardException("Cannot recreate application component '%s'", $componentName);
		$this->components[$componentName] = $instance;
	}
	
	/**
	 * @see ikarus\system\application.IApplication::boot()
	 */
	public function boot() {
		Ikarus::getEventManager()->fire($this, 'boot');
	}
	
	/**
	 * @see ikarus\system\application.IApplication::componentExists()
	 */
	public function componentExists($componentName) {
		return array_key_exists($componentName, $this->components);
	}
	
	/**
	 * @see ikarus\system\application.IApplication::getComponent()
	 */
	public function getComponent($componentName) {
		if (!$this->componentExists($componentName)) throw new StrictStandardException("Cannot get non-existing component '%s'", $componentName);
		return $this->components[$componentName];
	}
	
	/**
	 * @see ikarus\system\application.IApplication::getLibraryPath()
	 */
	public function getLibraryPath() {
		return $this->libraryPath;
	}
	
	/**
	 * @see ikarus\system\application.IApplication::getPackageID()
	 */
	public function getPackageID() {
		return $this->packageID;
	}
	
	/**
	 * Registers all default cache resources for this application
	 * @return			void
	 */
	protected function registerDefaultCacheResources() { }
	
	/**
	 * Registers all default components for this application
	 * @return			void
	 */
	protected function registerDefaultComponents() { }
	
	/**
	 * @see ikarus\system\application.IApplication::shutdown()
	 */
	public function shutdown() {
		Ikarus::getEventManager()->fire($this, 'shutdown');
	}
}
?>