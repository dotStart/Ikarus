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
use ikarus\system\exception\io\IOException;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\system\io\FilesystemDirectoryIterator;
use ikarus\system\io\FilesystemFile;
use ikarus\util\FileUtil;

/**
 * Implements disk filesystem methods
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DiskFilesystemAdapter implements IFilesystemAdapter {
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter
	 */
	public function __construct(array $adapterParameters = array()) { }
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::createDirectory()
	 */
	public function createDirectory($directoryName) {
		Ikarus::getFilesystemManager()->validatePath($directoryName);
		mkdir($directoryName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::createFile()
	 */
	public function createFile($fileName, $fileContent) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		file_put_contents($fileName, $fileContent);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::deleteDirectory()
	 */
	public function deleteDirectory($directoryName) {
		Ikarus::getFilesystemManager()->validatePath($directoryName);
		rmdir($directoryName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::deleteFile()
	 */
	public function deleteFile($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		unlink($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::fileExists()
	 */
	public function fileExists($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return file_exists($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::fileReadable()
	 */
	public function fileReadable($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return is_readable($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::fileWriteable()
	 */
	public function fileWritable($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return is_writable($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getCreationTime()
	 */
	public function getCreationTimestamp($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return filectime($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getDirectoryIterator()
	 */
	public function getDirectoryIterator($directoryName) {
		Ikarus::getFilesystemManager()->validatePath($directoryName);
		
		$iterator = new DirectoryIterator($directoryName);
		$elements = array();
		
		foreach($iterator as $element) {
			$elements[] = ($element->isDir() ? $this->getDirectoryIterator($element->getPathname()) : new FilesystemFile($element->getPathname(), $this));
		}
		
		return (new FilesystemDirectoryIterator($elements));
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getFilesize()
	 */
	public function getFilesize($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return filesize($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getModificationTime()
	 */
	public function getModificationTimestamp($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return filemtime($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getTemporaryDirectory()
	 */
	public function getTemporaryDirectory() {
		// document root
		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
			// get path
			$tempDirectory = FileUtil::addTrailingSlash($_SERVER['DOCUMENT_ROOT']).'tmp/';
			
			// create tmp folder if not already created
			if (!$this->isDirectory($tempDirectory)) { // XXX: This should work for strato webservers, too ...
				try {
					$this->createDirectory($tempDirectory);
					@chmod($tempDirectory, 0777);
				} catch (SystemException $ex) { } // ignore exceptions
			}
			
			if ($this->isDirectory($tempDirectory) and $this->fileWritable($tempDirectory)) return $tempDirectory;
		}
		
		// environment variables
		if (isset($_ENV['TMP']) or isset($_ENV['TEMP']) or isset($_ENV['TMPDIR'])) {
			$tempDirectory = FileUtil::addTrailingSlash((isset($_ENV['TMP']) ? $_ENV['TMP'] : (isset($_ENV['TEMP']) ? $_ENV['TEMP'] : $_ENV['TMPDIR'])));
			
			// try to create
			if (!$this->isDirectory($tempDirectory)) {
				try {
					$this->createDirectory($tempDirectory);
					@chmod($tempDirectory, 0777);
				} catch (SystemException $ex) { } // ignore exceptions
			}
			
			if ($this->isDirectory($tempDirectory) and $this->fileWritable($tempDirectory)) return $tempDirectory;
		}
		
		// upload_tmp_dir ini variable
		if (($tempDirectory = ini_get('upload_tmp_dir'))) {
			// try to create
			if (!$this->isDirectory($tempDirectory)) {
				try {
					$this->createDirectory($tempDirectory);
					@chmod($tempDirectory, 0777);
				} catch (SystemException $ex) { } // ignore exceptions
			}
			
			if ($this->isDirectory($tempDirectory) and $this->fileWritable($tempDirectory)) return $tempDirectory;
		}
		
		// /tmp/
		if ($this->isDirectory('/tmp/') and $this->fileWritable('/tmp/')) return '/tmp/';
		
		// session save path
		if (function_exists('session_save_path') and ($tempDirectory = session_save_path())) {
			// try to create
			if (!$this->isDirectory($tempDirectory)) {
				try {
					$this->createDirectory($tempDirectory);
					@chmod($tempDirectory, 0777);
				} catch (SystemException $ex) { }
			}
			
			if ($this->isDirectory($tempDirectory) and $this->fileWritable($tempDirectory)) return $tempDirectory;
		}
		
		// ikarus installation path
		$tempDirectory = Ikarus::getPath().'tmp/';
		
		// try to create
		if (!$this->isDirectory($tempDirectory)) {
			try {
				$this->createDirectory($tempDirectory);
				@chmod($tempDIrectory, 0777);
			} catch (SystemException $ex) { }
			
			if ($this->isDirectory($tempDirectory) and $this->fileWritable($tempDirectory)) return $tempDirectory;
		}
		
		// giving up
		throw new IOException('Tried 9001 temp directories. Could not find any suitable place for my garbage. Giving up.'); // some kind of easteregg
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::isDirectory()
	 */
	public function isDirectory($targetPath) {
		Ikarus::getFilesystemManager()->validatePath($targetPath);
		return is_dir($targetPath);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::isFile()
	 */
	public function isFile($targetPath) {
		Ikarus::getFilesystemManager()->validatePath($targetPath);
		return is_file($targetPath);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::isSupported()
	 */
	public static function isSupported() {
		return true;
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::modifyFile()
	 */
	public function modifyFile($fileName, $fileContent) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		file_put_contents($fileName, $fileContent);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::readFileContents()
	 */
	public function readFileContents($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return file_get_contents($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::shutdown()
	 */
	public function shutdown() { }
}
?>