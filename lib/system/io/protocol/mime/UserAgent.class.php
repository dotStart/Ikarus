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
namespace ikarus\system\io\protocol\mime;

/**
 * Represents a simple user agent.
 * Note: This class is a bit missplaced and should get some kind of own namespace.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class UserAgent {

	/**
	 * Stores the application name.
	 * @var                        string
	 */
	protected $applicationName = '';

	/**
	 * Stores the application version.
	 * @var                        string
	 */
	protected $applicationVersion = null;

	/**
	 * Stores the library name.
	 * @var                        string
	 */
	protected $libraryName = '';

	/**
	 * Stores the library version.
	 * @var                        string
	 */
	protected $libraryVersion = null;

	/**
	 * Stores the user agent's website (usually used for bots).
	 * @var                        string
	 */
	protected $website = null;

	/**
	 * Constructs the object.
	 * @param                        string $applicationName
	 * @param                        string $libraryName
	 * @param                        string $applicationVersion
	 * @param                        string $libraryVersion
	 * @param                        string $website
	 */
	public function __construct ($applicationName, $libraryName, $applicationVersion = null, $libraryVersion = null, $website = null) {
		$this->applicationName = $applicationName;
		$this->libraryName = $libraryName;
		$this->applicationVersion = $applicationVersion;
		$this->libraryVersion = $libraryVersion;
		$this->website = $website;
	}

	/**
	 * Builds a user-agent string.
	 * @return                        string                        string
	 */
	public function buildString () {
		return $this->applicationName . ($this->applicationVersion !== null ? '/' . $this->applicationVersion : '') . ' (' . $this->libraryName . ($this->libraryVersion !== null ? '/' . $this->libraryVersion : '') . ($this->website !== null ? '; +' . $this->website : '') . ')';
	}

	/**
	 * Returns the application name.
	 * @return                        string
	 */
	public function getApplicationName () {
		return $this->applicationName;
	}

	/**
	 * Returns the application version.
	 * @return                        string
	 */
	public function getApplicationVersion () {
		return $this->applicationVersion;
	}

	/**
	 * Returns the Ikarus Framework user agent.
	 * @return                        string
	 */
	public static final function getIkarusUserAgent () {
		return (new static('Ikarus Framework', 'PHP', IKARUS_VERSION, PHP_VERSION, 'http://www.ikarus-framework.de'));
	}

	/**
	 * Returns the library name.
	 * @return                        string
	 */
	public function getLibraryName () {
		return $this->libraryName;
	}

	/**
	 * Returns the library version.
	 * @return                        string
	 */
	public function getLibraryVersion () {
		return $this->libraryVersion;
	}

	/**
	 * Returns the website url.
	 * @return                        string
	 */
	public function getWebsite () {
		return $this->website;
	}

	/**
	 * Sets a new application name.
	 * @param                        string $name
	 * @return                        void
	 */
	public function setApplicationName ($name) {
		$this->applicationName = $name;
	}

	/**
	 * Sets a new application version.
	 * @param                        string $version
	 * @return                        void
	 */
	public function setApplicationVersion ($version) {
		$this->applicationVersion = $version;
	}

	/**
	 * Sets a new library name.
	 * @param                        string $name
	 * @return                        void
	 */
	public function setLibraryName ($name) {
		$this->libraryName = $name;
	}

	/**
	 * Sets a new library version.
	 * @param                        string $version
	 * @return                        void
	 */
	public function setLibraryVersion ($version) {
		$this->libraryVersion = $version;
	}

	/**
	 * Sets a new website URL.
	 * @param                        string $url
	 * @return                        void
	 */
	public function setWebsite ($url) {
		$this->website = $url;
	}

	/**
	 * Creates a string from this object.
	 * @return                        string
	 */
	public function __toString () {
		return $this->buildString ();
	}
}

?>