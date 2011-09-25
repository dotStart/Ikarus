<?php
namespace ikarus\system\io\adapter;
use ikarus\system\Ikarus;
use ikarus\system\io\FilesystemDirectoryIterator;
use ikarus\system\io\FilesystemFile;

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
	public function __construct($adapterParameters = array()) { }
	
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
	public function fileWriteable($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return is_writeable($fileName);
	}
	
	/**
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::getCreationTime()
	 */
	public function getCreationTime($fileName) {
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
	public function getModificationTime($fileName) {
		Ikarus::getFilesystemManager()->validatePath($fileName);
		return filemtime($fileName);
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
	 * @see ikarus\system\io\adapter.IFilesystemAdapter::isSupported()
	 */
	public static function isSupported() {
		return true;
	}
}
?>