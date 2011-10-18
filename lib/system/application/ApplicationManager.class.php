<?php
namespace ikarus\system\application;
use ikarus\system\Ikarus;
use ikarus\system\exception\ApplicationException;
use ikarus\system\exception\HiddenApplicationException;
use ikarus\system\exception\SystemException;
use ikarus\util\ClassUtil;
use ikarus\util\FileUtil;

/**
 * Manages all installed applications
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ApplicationManager {
	
	/**
	 * Contains a list of applications
	 * @var			array<Application>
	 */
	protected $applications = array();
	
	/**
	 * Creates a new instance of type ApplicationManager
	 */
	public function __construct() {
		$this->loadApplicationCache();
	}
	
	/**
	 * Returns true if the given application abbreviation exists
	 * @param			string			$abbreviation
	 * @return			boolean
	 */
	public function applicationAbbreviationExists($abbreviation) {
		return array_key_exists($abbreviation, $this->applications);
	}
	
	/**
	 * Alias for ApplicationManager::applicationAbbreviationExists()
	 * @see ikarus\system\application.ApplicationManager.applicationAbbreviationExists()
	 */
	public function applicationPrefixExists($prefix) {
		return $this->applicationAbbreviationExists($prefix);
	}
	
	/**
	 * Boots all applications
	 * @return			void
	 */
	public function boot() {
		if (!count($this->applications)) throw new SystemException("What the fuck? There are no applications! What are you doing?!");
		
		foreach($this->applications as $app) {
			$app->boot();
		}
	}
	
	/**
	 * Displays the given exception
	 * @param			SystemException			$ex
	 * @return			void
	 */
	public function displayErrorMessage(SystemException $ex) {
		$exception = new HiddenApplicationException($ex->getMessage(), $ex->getCode(), $ex);
		$exception->show();
	}
	
	/**
	 * Returns the application with given abbreviation
	 * @param			string			$abbreviation
	 * @throws			StrictStandardException
	 * @return			IApplication
	 */
	public function getApplication($abbreviation) {
		// strict standard
		if (!$this->applicationAbbreviationExists($abbreviation)) throw new StrictStandardException("The application abbreviation '%s' does not exist.", $abbreviation);
		
		return $this->applications[$abbreviation];
	}
	
	/**
	 * Handles application errors
	 * @param			PrintableException			$ex
	 * @return			void
	 */
	public function handleApplicationError(\Exception $ex) {
		if (get_class($ex) == 'ikarus\\system\\exception\\SystemException') return $ex->showMinimal();
		if (Ikarus::getConfiguration()->get('global.advanced.debug') or Ikarus::getConfiguration()->get('global.advanced.showErrors')) return $ex->showMinimal();
		
		$this->displayErrorMessage($ex);
	} 
	
	/**
	 * Loads the application cache
	 * @return			void
	 */
	protected function loadApplicationCache() {
		// handle developer mode
		$packageID = (defined('PACKAGE_ID') ? PACKAGE_ID : IKARUS_ID);
		
		// load cache
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('applications-'.$packageID, 'applications-'.$packageID, 'ikarus\system\cache\builder\CacheBuilderApplications');
		$applicationList = Ikarus::getCacheManager()->getDefaultAdapter()->get('applications-'.$packageID);
		
		// create application instances
		foreach($applicationList as $application) {
			// get class name
			$className = $application->className;
			
			// fix library path
			$application->libraryPath = FileUtil::getRealPath(IKARUS_DIR.$application->libraryPath);
			
			// !!!DIRTY FIX!!! We have to load the application core via require_once! This is not the correct way but the library path is not recognized!
			$includePath = $application->libraryPath.str_replace('\\', '/', substr($application->className, (stripos($application->className, '\\') + 1))).'.class.php';
			if (!file_exists($includePath)) throw new ApplicationException("Cannot read application core file '%s': No such file or directory", $includePath);
			require_once($includePath);
			
			// check parameters
			if (!class_exists($className, true)) throw new StrictStandardException("Cannot load application '%s' (%s): Class '%s' was not found", $application->applicationTitle, $application->applicationAbbreviation, $className);
			if (!ClassUtil::isInstanceOf($className, 'ikarus\\system\\application\\IApplication')) throw new StrictStandardException("Cannot load application '%s' (%s): Class '%s' is not an instance of IApplication", $application->applicationTitle, $application->applicationAbbreviation, $className);
			
			// create application instance
			$this->applications[$application->applicationAbbreviation] = new $className($application->applicationAbbreviation, $application->libraryPath, $application->packageID, (defined('IKARUS_ENVIRONMENT') ? IKARUS_ENVIRONMENT : 'frontend'));
		}
	}
	
	/**
	 * Calls the shutdown method of each active application
	 * @return			void
	 */
	public function shutdown() {
		foreach($this->applications as $application) {
			$application->shutdown();
		}
	}
}
?>