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
namespace ikarus\system\application;
use ikarus\system\event\application\ApplicationEventArguments;
use ikarus\system\event\application\BootEvent;
use ikarus\system\event\application\InitFinishedEvent;
use ikarus\system\event\application\ShutdownEvent;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;

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
	 * Contains the choosen environment
	 * @var			string
	 */
	protected $environment = '';
	
	/**
	 * Contains the instance ID.
	 * @var			string
	 */
	protected $instanceID = 0;

	/**
	 * Contains an library namespace for this application
	 * @var			string
	 */
	protected $libraryNamespace = '';

	/**
	 * Contains the packageID of this application
	 * @var			integer
	 */
	protected $packageID = 0;

	/**
	 * Contains true if this application is the primary application of this framework instance
	 * @var			boolean
	 */
	protected $primaryApplication = false;

	/**
	 * @see ikarus\system\application.IApplication::__construct()
	 */
	public function __construct($instanceID, $abbreviation, $libraryNamespace, $packageID, $environment, $primaryApplication = false) {
		$this->instanceID = $instanceID;
		$this->abbreviation = $abbreviation;
		$this->libraryNamespace = ClassUtil::buildPath($abbreviation, $libraryNamespace);
		$this->packageID = $packageID;
		$this->environment = $environment;
		$this->primaryApplication = $primaryApplication;

		// fire InitFinished event
		Ikarus::getEventManager()->fire(new InitFinishedEvent(new ApplicationEventArguments($this)));
	}

	/**
	 * @see ikarus\system\application.IApplication::boot()
	 */
	public function boot() {
		Ikarus::getEventManager()->fire(new BootEvent(new ApplicationEventArguments($this)));

		$this->registerDefaultCacheResources();
		$this->registerDefaultComponents();
	}

	/**
	 * @see ikarus\system\application.IApplication::getAbbreviation()
	 */
	public function getAbbreviation() {
		return $this->abbreviation;
	}

	/**
	 * @see ikarus\system\application.IApplication::getEnvironment()
	 */
	public function getEnvironment() {
		return $this->environment;
	}
	
	public function getInstanceID() {
		return $this->instanceID;
	}

	/**
	 * @see ikarus\system\application.IApplication::getLibraryNamespace()
	 */
	public function getLibraryNamespace() {
		return $this->libraryNamespace;
	}

	/**
	 * @see ikarus\system\application.IApplication::getPackageID()
	 */
	public function getPackageID() {
		return $this->packageID;
	}

	/**
	 * @see ikarus\system\application.IApplication::isPrimaryApplication()
	 */
	public function isPrimaryApplication() {
		return $this->primaryApplication;
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
		Ikarus::getEventManager()->fire(new ShutdownEvent(new ApplicationEventArguments($this)));

		$this->shutdownDefaultComponents();
	}

	/**
	 * @see ikarus\system\application.IApplication::shutdownDefaultComponents()
	 */
	public function shutdownDefaultComponents() { }
}
?>