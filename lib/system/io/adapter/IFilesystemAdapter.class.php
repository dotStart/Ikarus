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
namespace ikarus\system\io\adapter;

/**
 * Defines needed methods for filesystem adapters
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IFilesystemAdapter {
	
	/**
	 * Creates a new instance of type IFilesystemAdapter
	 * @param			array			$adapterParameters
	 * @throws			ikarus\system\exception\SystemException
	 */
	public function __construct($adapterParameters = array());
	
	/**
	 * Creates a new directory
	 * @param			string			$directoryName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			void
	 */
	public function createDirectory($directoryName);
	
	/**
	 * Creates a new file with given content
	 * @param			string			$fileName
	 * @param			string			$fileContent
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @throws			ikarus\system\exception\SystemException
	 * @return			void
	 */
	public function createFile($fileName, $fileContent);
	
	/**
	 * Deletes a directory
	 * @param			string			$directoryName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			void
	 */
	public function deleteDirectory($directoryName);
	
	/**
	 * Deletes a file
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @throws			ikarus\system\exception\SystemException
	 * @return			void
	 */
	public function deleteFile($fileName);
	
	/**
	 * Checks whether a file exists
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			boolean
	 */
	public function fileExists($fileName);
	
	/**
	 * Checks whether a file is readable
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			boolean
	 */
	public function fileReadable($fileName);
	
	/**
	 * Checks whether a file is writable
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			boolean
	 */
	public function fileWritable($fileName);
	
	/**
	 * Returns the creation time of a file as unix timestamp
	 * @param			string			$targetPath
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			integer
	 */
	public function getCreationTimestamp($fileName);

	/**
	 * Returns an iterator with all directory contents
	 * @param			string			$directoryName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			ikarus\system\io\FilesystemIterator
	 */
	public function getDirectoryIterator($directoryName);
	
	/**
	 * Returns the size of a file
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			integer
	 */
	public function getFilesize($fileName);
	
	/**
	 * Returns the modification time of a file as unix timestamp
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			integer
	 */
	public function getModificationTimestamp($fileName);
	
	/**
	 * Returns a path which is used to store files temporary.
	 * <strong>Note:</strong> This path is unique to this adapter and should NOT be used with other adapters.
	 * @return			string
	 * @throws			ikarus\system\exception\io\IOException
	 */
	public function getTemporaryDirectory();
	
	/**
	 * Checks whether the target is a directory
	 * @param			string			$targetPath
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			boolean
	 */
	public function isDirectory($targetPath);
	
	/**
	 * Checks whether the target is a file
	 * @param			string			$targetPath
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @return			boolean
	 */
	public function isFile($targetPath);
	
	/**
	 * Returns true if needed php side components are available
	 * @return			boolean
	 */
	public static function isSupported();
	
	/**
	 * Modifies an existing file
	 * @param			string			$fileName
	 * @param			string			$fileContent
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @throws			ikarus\system\exception\SystemException
	 * @return			void
	 */
	public function modifyFile($fileName, $fileContent);
	
	/**
	 * Reads the whole file content
	 * @param			string			$fileName
	 * @throws			ikarus\system\exception\io\IOException
	 * @throws			ikarus\system\exception\StrictStandardException
	 * @throws			ikarus\system\exception\SystemException
	 * @return			string
	 */
	public function readFileContents($fileName);
	
	/**
	 * Closes all filesystem adapter connections (if any)
	 * @return			void
	 */
	public function shutdown();
}
?>