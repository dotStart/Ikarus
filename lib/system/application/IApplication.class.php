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
	 * @param			integer			$instanceID
	 * @param			string			$abbreviation
	 * @param			string			$libraryNamespace
	 * @param			integer			$packageID
	 * @param			string			$environment
	 * @internal			New instances of this class are created by Ikarus ...
	 */
	public function __construct($instanceID, $abbreviation, $libraryNamespace, $packageID, $environment, $primaryApplication = false);
	
	/**
	 * Boots the application
	 * @return			void
	 * @api
	 */
	public function boot();
	
	/**
	 * Returns the application abbreviation
	 * @return			string
	 * @api
	 */
	public function getAbbreviation();
	
	/**
	 * Returns the choosen environment for this application instance
	 * @return			string
	 * @api
	 */
	public function getEnvironment();
	
	/**
	 * Returns the instanceID of this application.
	 * @return			integer
	 * @api
	 */
	public function getInstanceID();
	
	/**
	 * Returns the library namespace of this application
	 * @return			string
	 * @api
	 */
	public function getLibraryNamespace();
	
	/**
	 * Returns the packageID of this application
	 * @return			integer
	 * @api
	 */
	public function getPackageID();
	
	/**
	 * Returns true if this application is the main application of this framework instance
	 * @return			boolean
	 * @api
	 */
	public function isPrimaryApplication();
	
	/**
	 * Closes all application components
	 * @return			void
	 * @internal			This method will be called by Ikarus during it's shutdown period.
	 */
	public function shutdown();
	
	/**
	 * Shuts down all default components
	 * @return			void
	 * @internal			Thhis method will be called by Ikarus during it's shutdown period.
	 */
	public function shutdownDefaultComponents();
}
?>