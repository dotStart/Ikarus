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
namespace ikarus\system\io;
use ikarus\system\exception\SystemException;

/**
 * The RemoteFile class opens a connection to a remote host as a file.
 * @author		Originally developed by Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class RemoteFile extends File {
	protected $host;
	protected $port;
	protected $errorNumber = 0;
	protected $errorDesc = '';

	/**
	 * Opens a new connection to a remote host.
	 * @param 	string		$host
	 * @param 	string		$port
	 * @param 	integer		$timeout
	 */
	public function __construct($host, $port, $timeout = 30) {
		$this->host = $host;
		$this->port = $port;
		$this->resource = fsockopen($host, $port, $this->errorNumber, $this->errorDesc, $timeout);
		if ($this->resource === false) {
			throw new SystemException('Can not connect to ' . $host, 14000);
		}
	}

	/**
	 * Returns the error number of the last error.
	 * @return 	integer
	 */
	public function getErrorNumber() {
		return $this->errorNumber;
	}

	/**
	 * Returns the error description of last error.
	 * @return	string
	 */
	public function getErrorDesc() {
		return $this->errorDesc;
	}
}
?>