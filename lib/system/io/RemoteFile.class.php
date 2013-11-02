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

use ikarus\system\exception\io\IOException;

/**
 * Allows to open up connections to external resources.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class RemoteFile extends File {

	/**
	 * Stores the time which has to exceed before the connection is marked as failed.
	 * @var                        integer
	 */
	protected $connectionTimeout = 0;

	/**
	 * Stores the hostname of the connection target.
	 * @var                        string
	 */
	protected $hostname = '';

	/**
	 * Stores the port of the target.
	 * @var                        integer
	 */
	protected $port = 0;

	/**
	 * Stores the last error occurred.
	 * @var                        integer
	 */
	protected $errorNumber = 0;

	/**
	 * Stores the last error description.
	 * @var                        string
	 */
	protected $errorDescription = '';

	/**
	 * Opens a new connection to a remote host.
	 * @param                        string  $hostname
	 * @param                        string  $port
	 * @param                        integer $timeout
	 * @api
	 */
	public function __construct ($hostname, $port, $timeout = 30) {
		// store connection detail
		$this->hostname = $host;
		$this->port = $port;
		$this->connectionTimeout = $timeout;

		// create resource
		$this->createConnection ();
	}

	/**
	 * Creates the connection.
	 * @return                        void
	 * @throws                        IOException
	 * @internal                        This method gets called by __construct()
	 */
	public function createConnection () {
		// create connection
		$this->resource = fsockopen ($this->hostname, $this->port, $this->errorNumber, $this->errorDescription, $this->connectionTimeout);

		// validate resource
		if ($this->resource === false) throw new IOException('Cannot connect to %s:%u (%u): %s', $this->hostname, $this->port, $this->errorNumber, $this->errorDescription);
	}

	/**
	 * Returns the current connection timeout.
	 * @return                        integer
	 * @api
	 */
	public function getConnectionTimeout () {
		return $this->connectionTimeout;
	}

	/**
	 * Returns the error number of the last error.
	 * @return                        integer
	 * @api
	 */
	public function getErrorNumber () {
		return $this->errorNumber;
	}

	/**
	 * Returns the error description of last error.
	 * @return                        string
	 * @api
	 */
	public function getErrorDescription () {
		return $this->errorDescription;
	}

	/**
	 * Returns the current hostname.
	 * @return                        string
	 * @api
	 */
	public function getHostname () {
		return $this->hostname;
	}

	/**
	 * Returns the current port.
	 * @return                        integer
	 * @api
	 */
	public function getPort () {
		return $this->port;
	}

	/**
	 * Sets a new connection timeout.
	 * @param                        integer $timeout
	 * @return                        void
	 * 2@api
	 */
	public function setConnectionTimeout ($timeout) {
		$this->connectionTimeout = $timeout;
	}

	/**
	 * Sets a new hostname.
	 * @param                        string $hostname
	 * @return                        void
	 * @api
	 */
	public function setHostname ($hostname) {
		$this->hostname = $hostname;
	}

	/**
	 * Sets a new port.
	 * @param                        integer $port
	 * @return                        void
	 * @api
	 */
	public function setPort ($port) {
		$this->port = $port;
	}
}

?>