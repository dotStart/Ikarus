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
namespace ikarus\system\package;
use ikarus\system\Ikarus;

/**
 * Stores all information a package contains.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class PackageInformation {
	
	/**
	 * Contains information about a package's author.
	 * @var			PackageAuthorInformation
	 */
	protected $authorInformation = null;
	
	/**
	 * Contains a list of dependencies.
	 * @var			PackageDependency[]
	 */
	protected $dependencies = array();
	
	/**
	 * Contains a list of exclusions (incompatible packages).
	 * @var			ExcludedPackage[]
	 */
	protected $exclusions = array();
	
	/**
	 * Contains a list of installation instructions.
	 * @var			InstallationInstructionList
	 */
	protected $installationInstructions = array();
	
	/**
	 * Contains a list of optional packages.
	 * @var			OptionalPackage[]
	 */
	protected $optionalPackages = array();
	
	/**
	 * Contains information about the package itself.
	 * @var			UniquePackageInformation
	 */
	protected $packageInformation = null;
	
	/**
	 * Contains a list of soft dependencies.
	 * @var			SoftPackageDependency[]
	 */
	protected $softDependencies = array();
	
	/**
	 * Contains a list of update instructions.
	 * @var			UpdateInstruction[]
	 */
	protected $updateInstructions = array();
	
	/**
	 * Appends a dependency.
	 * @param			PackageDependency				$dependency
	 * @return			void
	 * @api
	 */
	public function appendDependency(PackageDependency $dependency) {
		$this->dependencies[] = $dependency;
	}
	
	/**
	 * Appends an exclusion.
	 * @param			PackageExclusion				$exclusion
	 * @return			void
	 * @api
	 */
	public function appendExclusion(PackageExclusion $exclusion) {
		$this->exclusions[] = $exclusion;
	}
	
	/**
	 * Appends an installation instruction.
	 * @param			InstallationInstruction				$instruction
	 * @return			void
	 * @api
	 */
	public function appendInstallationInstruction(InstallationInstruction $instruction) {
		$this->installationInstructions[] = $instruction;
	}
	
	/**
	 * Appends an optional package.
	 * @param			OptionalPackage					$package
	 * @return			void
	 * @api
	 */
	public function appendOptionalPackage(OptionalPackage $package) {
		$this->optionalPackages[] = $package;
	}
	
	/**
	 * Appends a soft dependency
	 * @param			SoftDependency					$dependency
	 * @return			void
	 * @api
	 */
	public function appendSoftDependency(SoftDependency $dependency) {
		$this->softDependencies[] = $dependency;
	}
	
	/**
	 * Appends an update instruction.
	 * @param			UpdateInstruction				$instruction
	 * @return			void
	 * @api
	 */
	public function appendUpdateInstruction(UpdateInstruction $instruction) {
		$this->updateInstructions[] = $instruction;
	}
	
	/**
	 * Decodes package data (Which has been pre-processed by the PackageFileReader class).
	 * @param			string			$data
	 * @return			self
	 * @api
	 */
	public static function decode($data) {
		// decode json string
		$data = json_decode($data);
		
		// create new instance
		$instance = new static();
		
		// iterate over data
		foreach($data as $key => $val) {
			switch($key) {
				case 'authorInformation':		$instance->setAuthorInformation(PackageAuthorInformation::decode($val)); break;
				case 'dependencies':			$instance->setDependencies(PackageDependency::decode($val)); break;
				case 'exclusions':			$instance->setExclusions(ExcludedPackage::decode($val)); break;
				case 'installationInstructions':	$instance->setInstallationInstructions(InstallationInstructionList::decode($val)); break;
				case 'optionalPackages':		$instance->setOptionalPackages(OptionalPackage::decode($val)); break;
				case 'packageInformation':		$instance->setPackageInformation(UniquePackageInformation::decode($val)); break;
				case 'softDependencies':		$instance->setSoftDependencies(SoftDependency::decode($val)); break;
				case 'updateInstructions':		$instance->setUpdateInstructions(UpdateInstruction::decode($val)); break;
				default:
					// fire event
					$event = new ElementDecodeFailed(new ElementEventArguments($key, $val, $instance));
					Ikarus::getEventManager()->fire($event);
					
					// throw exception (if needed)
					if (!$event->isCancelled()) throw new CorruptedPackageFileException('Unknown package definition part "%s" detected', $key);
					break;
			}
		}
		
		return $instance;
	}
	
	/**
	 * Sets the author information.
	 * @param			PackageAuthorInformation			$information
	 * @return			void
	 * @api
	 */
	public function setAuthorInformation(PackageAuthorInformation $information) {
		$this->authorInformation = $information;
	}
	
	/**
	 * Sets the dependency list.
	 * @param			PackageDependency[]				$dependencies
	 * @return			void
	 * @api
	 */
	public function setDependencies($dependencies) {
		$this->dependencies = $dependencies;
	}
	
	/**
	 * Sets the exclusion list.
	 * @param			PackageExclusion[]				$exclusions
	 * @return			void
	 * @api
	 */
	public function setExclusions($exclusions) {
		$this->exclusions = $exclusions;
	}
	
	/**
	 * Sets the installation instruction list.
	 * @param			InstallationInstruction[]			$instructions
	 * @return			void
	 * @api
	 */
	public function setInstallationInstructions($instructions) {
		$this->installationInstructions = $instructions;
	}
	
	/**
	 * Sets the list of optional packages.
	 * @param			OptionalPackage[]				$packages
	 * @return			void
	 * @api
	 */
	public function setOptionalPackages($packages) {
		$this->optionalPackages = $packages;
	}
	
	/**
	 * Sets the package information.
	 * @param			UniquePackageInformation			$information
	 * @return			void
	 * @api
	 */
	public function setPackageInformation(UniquePackageInformation $information) {
		$this->packageInformation = $information;
	}
	
	/**
	 * Sets the list of soft dependencies.
	 * @param			SoftPackageDependency[]				$dependencies
	 * @return			void
	 * @api
	 */
	public function setSoftDependencies($dependencies) {
		$this->softDependencies = $dependencies;
	}
	
	/**
	 * Sets the list of update instructions.
	 * @param			UpdateInstruction[]				$instructions
	 * @return			void
	 * @api
	 */
	public function setUpdateInstructions($instructions) {
		$this->updateInstructions = $instructions;
	}
	
	/**
	 * Returns the author information object.
	 * @return			PackageAuthorInformation
	 * @api
	 */
	public function getAuthorInformation() {
		return $this->authorInformation;
	}
	
	/**
	 * Returns the dependency list.
	 * @return			PackageDependency[]
	 * @api
	 */
	public function getDependencies() {
		return $this->dependencies;
	}
	
	/**
	 * Returns the exclusion list.
	 * @return			ExcludedPackage[]
	 * @api
	 */
	public function getExclusions() {
		return $this->exclusions;
	}
	
	/**
	 * Returns the installation instruction object.
	 * @return			InstallationInstructionList
	 * @api
	 */
	public function getInstallationInstructions() {
		return $this->installationInstructions;
	}
	
	/**
	 * Returns the optional package list.
	 * @return			OptionalPackage[]
	 * @api
	 */
	public function getOptionalPackages() {
		return $this->optionalPackages;
	}

	/**
	 * Returns the package information object.
	 * @return			UniquePackageInformation
	 * @api
	 */
	public function getPackageInformation() {
		return $this->packageInformation;
	}
	
	/**
	 * Returns the soft dependency list.
	 * @return			SoftPackageDependency[]
	 * @api
	 */
	public function getSoftDependencies() {
		return $this->softDependencies;
	}
	
	/**
	 * Returns the update instruction list.
	 * @return			UpdateInstruction[]
	 * @api
	 */
	public function getUpdateInstructions() {
		return $this->updateInstructions;
	}
}
?>