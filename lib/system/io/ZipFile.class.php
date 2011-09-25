<?php
namespace ikarus\system\io;
use ikarus\system\exception\SystemException;

/**
 * The File class handles all file operations on a zipped file.
 *
 * @author		Originally developed by Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ZipFile extends File {
	/**
	 * Opens a new zipped file.
	 *
	 * @param  	string		$filename
	 * @param 	string		$mode
	 */
	public function __construct($filename, $mode = 'wb') {
		$this->filename = $filename;
		if (!function_exists('gzopen')) {
			throw new SystemException('Can not find functions of the zlib extension', 11004);
		}
		$this->resource = gzopen($filename, $mode);
		/*if ($this->resource === false) {
			throw new SystemException('Can not open file ' . $filename, 11012);
			}*/
	}

	/**
	 * Calls the specified function on the open file.
	 *
	 * @param 	string		$function
	 * @param 	array		$arguments
	 */
	public function __call($function, $arguments) {
		if (function_exists('gz' . $function)) {
			array_unshift($arguments, $this->resource);
			return call_user_func_array('gz' . $function, $arguments);
		}
		else if (function_exists($function)) {
			array_unshift($arguments, $this->filename);
			return call_user_func_array($function, $arguments);
		}
		else {
			throw new SystemException('Can not call method ' . $function, 11003);
		}
	}

	/**
	 * Returns the filesize of the unzipped file.
	 *
	 * @return	integer
	 */
	public function getFileSize() {
		$byteBlock = 1<<14;
		$eof = $byteBlock;

		// the correction is for zip files that are too small
		// to get in the first while loop
		$correction = 1;
		while ($this->seek($eof) == 0) {
			$eof += $byteBlock;
			$correction = 0;
		}

		while ($byteBlock > 1) {
			$byteBlock >>= 1;
			$eof += $byteBlock * ($this->seek($eof) ? -1 : 1);
		}

		if ($this->seek($eof) == -1) $eof -= 1;

		$this->rewind();
		return $eof - $correction;
	}
}
?>
