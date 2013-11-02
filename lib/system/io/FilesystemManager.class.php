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
namespace ikarus\system\io;

use ikarus\system\event\EmptyEventArguments;
use ikarus\system\event\io\AdapterEventArguments;
use ikarus\system\event\io\CreateFileHandleEvent;
use ikarus\system\event\io\DefaultAdapterSetEvent;
use ikarus\system\event\io\FilenameEventArguments;
use ikarus\system\event\io\ShutdownEvent;
use ikarus\system\exception\StrictStandardException;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;

/**
 * Manages filesystem actions
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class FilesystemManager {

	/**
	 * Contains a prefix used for adapter class names
	 * @var                        string
	 */
	const FILESYSTEM_ADAPTER_CLASS_PREFIX = 'ikarus\\system\\io\\adapter\\';

	/**
	 * Contains a suffix used for adapter class names
	 * @var                        string
	 */
	const FILESYSTEM_ADAPTER_CLASS_SUFFIX = 'FilesystemAdapter';

	/**
	 * Contains all available connection handles
	 * @var                        array<ikarus\system\io\adapter\IFilesystemAdapter>
	 */
	protected $connections = array();

	/**
	 * Contains the current default adapter
	 * @var                        ikarus\ystem\io\adapter\IFilesystemAdapter
	 */
	protected $defaultAdapter = null;

	/**
	 * Contains a list of loaded adapters
	 * @var                        array<string>
	 */
	protected $loadedAdapters = array();

	/**
	 * Checks whether the given adapter is loaded or not
	 * @param                        string $adapterName
	 * @return                        boolean
	 * @api
	 */
	public function adapterIsLoaded ($adapterName) {
		return in_array ($adapterName, $this->loadedAdapters);
	}

	/**
	 * Creates a new filesystem connection
	 * @param                        string $adapterName
	 * @param                        array  $adapterParameters
	 * @param                        string $linkID
	 * @throws                        SystemException
	 * @return                        ikarus\system\io\adapter\IFilesystemAdapter
	 * @api
	 */
	public function createConnection ($adapterName, array $adapterParameters = array(), $linkID = null) {
		// validate adapter name
		if (!$this->adapterIsLoaded ($adapterName)) throw new SystemException("Cannot create a new connection with filesystem adapter '%s': The adapter was not loaded");

		// get class name
		$className = static::FILESYSTEM_ADAPTER_CLASS_PREFIX . ucfirst ($adapterName) . static::FILESYSTEM_ADAPTER_CLASS_SUFFIX;

		// create instance
		$adapter = new $className($adapterParameters);

		// save
		if ($linkID !== null) $this->connections[$linkID] = $adapter;

		return $this->connections[] = $adapter;
	}

	/**
	 * Creates a new file handle
	 * @param                        string $fileName
	 * @return                        ikarus\system\io\FilesystemHandle
	 * @api
	 */
	public function createFile ($fileName) {
		// events ...
		if (Ikarus::getEventManager () !== null) {
			// fire event
			$event = new CreateFileHandleEvent(new FilenameEventArguments($fileName));
			Ikarus::getEventManager ()->fire ($event);

			// cancellable event
			if ($event->isCancelled ()) return;
		}

		// create new filehandle
		return (new FilesystemHandle($fileName, true));
	}

	/**
	 * Returns the connection with the given linkID
	 * @param                        string $linkID
	 * @return                        ikarus\system\io\adapter\IFilesystemAdapter
	 * @api
	 */
	public function getConnection ($linkID) {
		if (isset($this->connections[$linkID])) return $this->connections[$linkID];

		return null;
	}

	/**
	 * Returns the current active default adapter
	 * @return                        ikarus\system\io\adapter\IFilesystemAdapter
	 * @api
	 */
	public function getDefaultAdapter () {
		return $this->defaultAdapter;
	}

	/**
	 * Loads a filesystem adapter
	 * @param                        string $adapterName
	 * @throws                        SystemException
	 * @throws                        StrictStandardException
	 * @return                        void
	 * @api
	 */
	public function loadAdapter ($adapterName) {
		// get class name
		$className = static::FILESYSTEM_ADAPTER_CLASS_PREFIX . ucfirst ($adapterName) . static::FILESYSTEM_ADAPTER_CLASS_SUFFIX;

		// validate class
		if (!class_exists ($className)) throw new SystemException("Cannot load filesystem adapter '%s': The adapter class '%s' does not exist", $adapterName, $className);
		if (!ClassUtil::isInstanceOf ($className, 'ikarus\system\io\adapter\IFilesystemAdapter')) throw new StrictStandardException("Cannot load filesystem adapter '%s': The adapter class '%s' does not implement ikarus\\system\\io\\adapter\\IfilesystemAdapter");

		// check for php side support
		if (!call_user_func (array($className, 'isSupported'))) throw new SystemException("Cannot create a new connection with filesystem adapter '%s': The adapter is not supported by php");

		// save adapter
		$this->loadedAdapters[] = $adapterName;
	}

	/**
	 * Sets the default adapter
	 * @param                        ikarus\system\io\adapter\IFilesystemAdapter $handle
	 * @return                        void
	 * @api
	 */
	public function setDefaultAdapter (adapter\IFilesystemAdapter $handle) {
		$this->defaultAdapter = $handle;

		// fire event
		if (Ikarus::getEventManager () !== null) Ikarus::getEventManager ()->fire (new DefaultAdapterSetEvent(new AdapterEventArguments($handle)));
	}

	/**
	 * Closes all filesystem connections
	 * @return                        void
	 * @internal                        This method gets called by Ikarus during it's shutdown period.
	 */
	public function shutdown () {
		// shutdown filesystem adapters
		foreach ($this->connections as $connection) {
			$connection->shutdown ();
		}

		// fire event
		if (Ikarus::getEventManager () !== null) Ikarus::getEventManager ()->fire (new ShutdownEvent(new EmptyEventArguments()));
	}

	/**
	 * Starts the default adapter
	 * @return                        void
	 * @internal                        This method gets called automatically during init.
	 */
	public function startDefaultAdapter () {
		// load adapter
		$this->loadAdapter (Ikarus::getConfiguration ()->get ('filesystem.general.defaultAdapter'));

		// create new connection
		$handle = $this->createConnection (Ikarus::getConfiguration ()->get ('filesystem.general.defaultAdapter'), (Ikarus::getConfiguration ()->get ('filesystem.general.adapterParameters') !== null ? Ikarus::getConfiguration ()->get ('filesystem.general.adapterParameters') : array()));

		// set as default
		$this->setDefaultAdapter ($handle);
	}

	/**
	 * Validates paths from filesystem adapters
	 * @param                        string $path
	 * @throws                        StrictStandardException
	 * @return                        void
	 * @api
	 */
	public function validatePath ($path) {
		// disallow relative paths
		if ($path{0} == '.') throw new StrictStandardException("Relative paths are not allowed");
	}
}

?>