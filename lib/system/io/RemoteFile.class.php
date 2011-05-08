<?php
namespace ikarus\system\io;
use ikarus\system\exception\SystemException;

/**
 * The RemoteFile class opens a connection to a remote host as a file.
 * @author		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
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