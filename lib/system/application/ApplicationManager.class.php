<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\application;

use ikarus\system\exception\ApplicationException;
use ikarus\system\exception\HiddenApplicationException;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;

/**
 * Manages all installed applications
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class ApplicationManager {

	/**
	 * Contains a list of applications
	 * @var                        array<Application>
	 */
	protected $applications = array();

	/**
	 * Creates a new instance of type ApplicationManager.
	 * @api
	 */
	public function __construct () {
		$this->loadApplicationCache ();
	}

	/**
	 * Returns true if the given application abbreviation exists
	 * @param                        string $abbreviation
	 * @return                        boolean
	 * @api
	 */
	public function applicationAbbreviationExists ($abbreviation) {
		return array_key_exists ($abbreviation, $this->applications);
	}

	/**
	 * Alias for ApplicationManager::applicationAbbreviationExists()
	 * @see ikarus\system\application.ApplicationManager.applicationAbbreviationExists()
	 */
	public function applicationPrefixExists ($prefix) {
		return $this->applicationAbbreviationExists ($prefix);
	}

	/**
	 * Boots all applications
	 * @return                        void
	 * @internal                        This method should only be used by Ikarus itself.
	 */
	public function boot () {
		if (!count ($this->applications)) throw new SystemException("What the fuck? There are no applications! What are you doing?!");

		foreach ($this->applications as $app) {
			$app->boot ();
		}
	}

	/**
	 * Detects the instance for current request.
	 * @return                        void
	 * @todo                        This does not work for CLI applications.
	 */
	protected function detectInstance () {
		// get cache
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ('instances', 'ikarus\\system\\cache\\builder\\application\\Instances');
		$instances = Ikarus::getCacheManager ()->getDefaultAdapter ()->get ('instances');

		// get instanceID
		foreach ($instances as $instance) {
			if ($instance->getDomainName () and $instance->getDomainName () != $_SERVER['HTTP_HOST']) continue;
			if ($instance->getDocumentRoot () and $instance->getDocumentRoot () != dirname ($_SERVER['PHP_SELF'])) continue;

			return $instance;
		}

		// something went wrong?!
		throw new ApplicationException("There is no matching application instance");
	}

	/**
	 * Displays the given exception
	 * @param                        SystemException $ex
	 * @return                        void
	 * @api
	 */
	public function displayErrorMessage (SystemException $ex) {
		$exception = new HiddenApplicationException($ex->getMessage (), $ex->getCode (), $ex);
		$exception->show ();
	}

	/**
	 * Returns the application with given abbreviation
	 * @param                        string $abbreviation
	 * @throws                        StrictStandardException
	 * @return                        IApplication
	 * @api
	 */
	public function getApplication ($abbreviation) {
		// strict standard
		if (!$this->applicationAbbreviationExists ($abbreviation)) throw new StrictStandardException("The application abbreviation '%s' does not exist.", $abbreviation);

		return $this->applications[$abbreviation];
	}

	/**
	 * Returns a list of all loaded applications
	 * @return                        array<ikarus\system\application\IApplication>
	 * @api
	 */
	public function getApplicationList () {
		return $this->applications;
	}

	/**
	 * Handles application errors
	 * @param                        PrintableException $ex
	 * @return                        void
	 * @api
	 */
	public function handleApplicationError (\Exception $ex) {
		if (ClassUtil::isInstanceOf ($ex, 'ikarus\\system\\exception\\NamedUserException')) return $ex->show ();
		if (get_class ($ex) == 'ikarus\\system\\exception\\SystemException' or Ikarus::getConfiguration ()->get ('global.advanced.debug') or Ikarus::getConfiguration ()->get ('global.advanced.showErrors')) return $ex->showMinimal ();
		$this->displayErrorMessage ($ex);
	}

	/**
	 * Loads the application cache
	 * @return                        void
	 */
	protected function loadApplicationCache () {
		// handle developer mode
		$instance = $this->detectInstance ();

		// load cache
		Ikarus::getCacheManager ()->getDefaultAdapter ()->createResource ('applications-' . $instance->getPackageID (), 'ikarus\\system\\cache\\builder\\application\\Applications');
		$applicationList = Ikarus::getCacheManager ()->getDefaultAdapter ()->get ('applications-' . $instance->getPackageID ());

		// create application instances
		foreach ($applicationList as $application) {
			// get class name
			$className = $application->className;

			// !!!DIRTY FIX!!! We have to load the application core via require_once! This is not the correct way but the library path is not recognized!
			$includePath = IKARUS_DIR . ($application->relativeApplicationPath !== null ? $application->relativeApplicationPath : '.') . '/lib/' . str_replace ('\\', '/', substr ($application->className, (stripos ($application->className, '\\') + 1))) . '.class.php';
			if (!file_exists ($includePath)) throw new ApplicationException("Cannot read application core file '%s': No such file or directory", $includePath);
			require_once ($includePath);

			// check parameters
			if (!class_exists ($className, true)) throw new StrictStandardException("Cannot load application '%s' (%s): Class '%s' was not found", $application->applicationTitle, $application->applicationAbbreviation, $className);
			if (!ClassUtil::isInstanceOf ($className, 'ikarus\\system\\application\\IApplication')) throw new StrictStandardException("Cannot load application '%s' (%s): Class '%s' is not an instance of IApplication", $application->applicationTitle, $application->applicationAbbreviation, $className);

			// create application instance
			$this->applications[$application->applicationAbbreviation] = new $className($application->applicationAbbreviation, $application->libraryNamespace, $application->packageID, (defined ('IKARUS_ENVIRONMENT') ? IKARUS_ENVIRONMENT : 'frontend'), ($instance->getPackageID () == $application->packageID ? true : false));
		}
	}

	/**
	 * Calls the shutdown method of each active application
	 * @return                        void
	 * @internal                        This method should only be called by Ikarus itsel during it's shutdown period.
	 */
	public function shutdown () {
		foreach ($this->applications as $application) {
			$application->shutdown ();
		}
	}
}

?>