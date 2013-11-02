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

use ikarus\util\FileUtil;

/**
 * Stores file information and provides standard access methods.
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class FilesystemFile implements IFileInfo {

	/**
	 * Contains the parent filesystem adapter.
	 * @var                        IFilesystemAdapter
	 */
	protected $adapter = null;

	/**
	 * Contains the full path to file.
	 * @var                        string
	 */
	protected $filePath = null;

	/**
	 * Constructs the object.
	 * @param                        string                     $filePath
	 * @param                        adapter\IFilesystemAdapter $adapter
	 * @internal                        New instances should get constructed by the parent adapter.
	 */
	public function __construct ($filePath, adapter\IFilesystemAdapter $adapter) {
		$this->adapter = $adapter;
		$this->filePath = $filePath;
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::deleteFile()
	 */
	public function delete () {
		return $this->adapter->deleteFile ($this->filePath);
	}

	/**
	 * @see ikarus\util.FileUtil::getDirectoryName()
	 */
	public function dirname () {
		return FileUtil::getDirectoryName ($this->filePath);
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getCreationTimestamp()
	 */
	public function getCreationTimestamp () {
		return $this->adapter->getCreationTimestamp ($this->filePath);
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getFilesize()
	 */
	public function getFilesize () {
		return $this->adapter->getFilesize ($this->filePath);
	}

	/**
	 * @see ikarus\system\io.IFileInfo::isDirectory()
	 */
	public function isDirectory () {
		return false;
	}

	/**
	 * @see ikarus\system\io.IFileInfo::isFile()
	 */
	public function isFile () {
		return true;
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::fileReadable()
	 */
	public function isReadable () {
		return $this->adapter->fileReadable ($this->filePath);
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::fileWritable()
	 */
	public function isWritable () {
		return $this->adapter->fileWritable ($this->filePath);
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::modifyFile()
	 */
	public function modifyFile ($fileContent) {
		return $this->adapter->modifyFile ($this->filePath, $fileContent);
	}

	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::readFileContents()
	 */
	public function readFileContents () {
		return $this->adapter->modifyFile ($this->filePath);
	}
}

?>