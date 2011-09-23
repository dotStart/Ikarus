<?php
namespace ikarus\system\io;
use ikarus\system\Ikarus;
use ikarus\util\FileUtil;

/**
 * Manages filesystem actions
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class FilesystemManager {
	
	/**
	 * Contains the current FTP connection (if any)
	 * @var				ikarus\system\io\FTP
	 */
	protected $ftpConnection = null;
	
	/**
	 * Creates a new FTP connection (if needed)
	 * @return			void
	 */
	public function createConnection() {
		// create FTP connection if needed
		if (Ikarus::getConfiguration()->get('filesystem.general.useFtp') and $this->ftpConnection === null) {
			$this->ftpConnection = new FTP(Ikarus::getConfiguration()->get('filesystem.general.ftpHostname'), Ikarus::getConfiguration()->get('filesystem.general.ftpPort'));
			$this->ftpConnection->login(Ikarus::getConfiguration()->get('filesystem.general.ftpUsername'), Ikarus::getConfiguration()->get('filsystem.general.ftpPassword'));
			$this->ftpConnection->chdir(Ikarus::getConfiguration()->get('filesystem.general.ftpDirectory'));
		}
	}
	
	/**
	 * Creates a new FilesystemHandle
	 * @param			string			$fileName
	 * @return			ikarus\system\io\FilesystemHandle
	 */
	public function createFile($fileName) {
		return (new FilesystemHandle($fileName, true));
	}
	
	public function readFileContents($fileName) {
		// readable?
		if (!is_readable($fileName) and !Ikarus::getConfiguration()->get('filesystem.general.useFtp')) throw new SystemException("Cannot read file '%s'");
		
		// default filesystem access
		if (is_readable($fileName)) return file_get_contents($fileName);
		
		// ftp
		$this->createConnection();
		
		// calculate path
		$filePath = FileUtil::getRelativePath(IKARUS_DIR, dirname($fileName)).basename($fileName);
	}
	
	/**
	 * Writes given content to given file
	 * @param			string			$fileName
	 * @param			string			$content
	 * @throws			SystemException
	 * @return			void
	 */
	public function writeFile($fileName, $content) {
		// validate path
		if ($fileName{0} == '.') throw new SystemException("You really should not use relative paths!");
		
		// ftp
		if (Ikarus::getConfiguration()->get('filesystem.general.useFtp')) {
			// calculate path
			$filePath = FileUtil::getRelativePath(IKARUS_DIR, dirname($fileName)).basename($fileName);
			
			// create FTP connection
			$this->createConnection();
			
			// create dummy file
			$dummyFileName = FileUtil::getTemporaryFilename('filesystem_', '.dat');
			$dummyFile = new File($dummyFileName);
			
			// write contents
			$dummyFile->write($content);
			$dummyFile->flush();
			
			// reset file pointer
			rewind($dummyFile->getResource());
			
			// upload
			$this->ftpConnection->fput($filePath, $dummyFile->getResource(), FTP_ASCII);
			
			// close dummy file
			$dummyFile->close();
			
			// delete file
			@$dummyFile->unlink();
		} else {
			$file = new File($fileName);
			$file->write($content);
			$file->flush();
			$file->close();
		}
	}
}
?>