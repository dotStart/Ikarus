<?php
namespace ikarus\system\application;

/**
 * Defines default methods for applications
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IApplication {
	
	/**
	 * Creates a new instance of type IApplication
	 * @param			string			$abbreviation
	 * @param			string			$libraryPath
	 * @param			integer			$packageID
	 */
	public function __construct($abbreviation, $libraryPath, $packageID);
	
	/**
	 * Adds a new component to application
	 * @param			string			$componentName
	 * @param			mixed			$instance
	 * @return			void
	 */
	public function addComponent($componentName, $instance);
	
	/**
	 * Boots the application
	 * @return			void
	 */
	public function boot();
	
	/**
	 * Checks whether the specified component exists
	 * @param			string			$componentName
	 * @return			boolean
	 */
	public function componentExists($componentName);
	
	/**
	 * Returns the specified component
	 * @param			string			$componentName
	 * @return			mixed
	 */
	public function getComponent($componentName);
	
	/**
	 * Returns the library path of this application
	 * @return			string
	 */
	public function getLibraryPath();
	
	/**
	 * Returns the packageID of this application
	 * @return			integer
	 */
	public function getPackageID();
	
	/**
	 * Closes all application components
	 * @return			void
	 */
	public function shutdown();
}
?>