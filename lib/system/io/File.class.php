<?php
namespace ikarus\system\io;
use ikarus\system\exception\SystemException;

/**
 * The File class handles all file operations.
 *
 * Example:
 * using php functions:
 * $fp = fopen('filename', 'wb');
 * fwrite($fp, '...');
 * fclose($fp);
 *
 * using this class:
 * $file = new File('filename');
 * $file->write('...');
 * $file->close();
 *
 * @author		Marcel Werk, Johannes Donath
 * @copyright		2001-2009 WoltLab GmbH, 2011 Evil-Co.de
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class File {
	
	/**
	 * Contains the file resource
	 * @var		resource
	 */
	protected $resource = null;
	
	/**
	 * Contains the filename
	 * @var		string
	 */
	protected $filename;

	/**
	 * Opens a new file.
	 *
	 * @param 	string		$filename
	 * @param 	string		$mode
	 */
	public function __construct($filename, $mode = 'wb') {
		$this->filename = $filename;
		$this->resource = fopen($filename, $mode);
		if ($this->resource === false) {
			throw new SystemException('Can not open file ' . $filename, 11012);
		}
	}
	
	/**
	 * Returnes the given filename
	 * @return		string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * Calls the specified function on the open file.
	 * Do not call this function directly. Use $file->write('') instead.
	 *
	 * @param 	string		$function
	 * @param 	array		$arguments
	 */
	public function __call($function, $arguments) {
		if (function_exists('f' . $function)) {
			array_unshift($arguments, $this->resource);
			return call_user_func_array('f' . $function, $arguments);
		}
		else if (function_exists($function)) {
			array_unshift($arguments, $this->filename);
			return call_user_func_array($function, $arguments);
		}
		else {
			throw new SystemException('Can not call file method ' . $function, 11003);
		}
	}
}
?>