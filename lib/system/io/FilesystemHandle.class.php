<?php
namespace ikarus\system\io;
use ikarus\system\Ikarus;

/**
 * Manages filesystem actions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class FilesystemHandle {
	
	/**
	 * Contains cached file contents
	 * @var			string
	 */
	protected $buffer = '';
	
	/**
	 * Contains the name of the new file (Absolute path)
	 * @var			string
	 */
	protected $fileName = '';
	
	/**
	 * Creates a new file if true
	 * @var			string
	 */
	protected $newFile = false;
	
	/**
	 * Creates a new instance of FilesystemHandle
	 * @param			string			$fileName
	 * @param			boolean			$newFile
	 */
	public function __construct($fileName, $newFile = false, ikarus\system\io\adapter\IFilesystemAdapter $adapter = null) {
		$this->fileName = $fileName;
		$this->newFile = $newFile;
		if (!$newFile) $this->buffer = ($adapter === null ? Ikarus::getFilesystemManager()->getDefaultAdapter()->readFileContents($this->fileName) : $adapter->readFileContents($this->fileName));
	}
	
	/**
	 * Adds the given content to buffer
	 * @param			string			$content
	 * @return			void
	 */
	public function append($content) {
		$this->buffer .= $content;
	}
	
	/**
	 * Replaces the current buffer with new content
	 * @param			string			$content
	 * @return			void
	 */
	public function setContent($content) {
		$this->buffer = $content;
	}
	
	/**
	 * @see ikarus\system\io.FilesystemManager::writeFile()
	 */
	public function write(ikarus\system\io\adapter\IFilesystemAdapter $adapter = null) {
		if ($adapter === null) $adapter = Ikarus::getFilesystemManager()->getDefaultAdapter();
		if ($this->newFile)
			$adapter->createFile($this->fileName, $this->buffer);
		else
			$adapter->modifyFile($this->fileName, $this->buffer);
	}
	
	/**
	 * Converts the file buffer to string
	 * @return			string
	 */
	public function __toString() {
		return $this->buffer;
	}
}
?>