<?php
namespace ikarus\system\io;
use ikarus\system\exception\SystemException;

/**
 * The FTP class handles all ftp operations.
 * @author		Originally developed by Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
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