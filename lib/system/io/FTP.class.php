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
 * The FTP class handles all ftp operations.
 * @author		Originally developed by Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class FTP {
	protected $resource;

	/**
	 * Opens a new ftp connection to given host.
	 * @param 	string		$host
	 * @param 	string		$port
	 * @param 	integer		$timeout
	 */
	public function __construct($host = 'localhost', $port = 21, $timeout = 30) {
		$this->resource = ftp_connect($host, $port, $timeout);
		if ($this->resource === false) {
			throw new SystemException('Can not connect to ' . $host, 11008);
		}
	}

	/**
	 * Calls the specified function on the open ftp connection.
	 * @param 	string		$function
	 * @param 	array		$arguments
	 */
	public function __call($function, $arguments) {
		array_unshift($arguments, $this->resource);
		if (!function_exists('ftp_'.$function)) {
			throw new SystemException('Can not call method ' . $function, 11003);
		}

		return call_user_func_array('ftp_' . $function, $arguments);
	}
}
?>