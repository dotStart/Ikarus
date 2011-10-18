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
	 * @param			string			$environment
	 */
	public function __construct($abbreviation, $libraryPath, $packageID, $environment, $primaryApplication = false);
	
	/**
	 * Boots the application
	 * @return			void
	 */
	public function boot();
	
	/**
	 * Returns the application abbreviation
	 * @return			string
	 */
	public function getAbbreviation();
	
	/**
	 * Returns the choosen environment for this application instance
	 * @return			string
	 */
	public function getEnvironment();
	
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
	 * Returns true if this application is the main application of this framework instance
	 * @return			boolean
	 */
	public function isPrimaryApplication();
	
	/**
	 * Closes all application components
	 * @return			void
	 */
	public function shutdown();
}
?>