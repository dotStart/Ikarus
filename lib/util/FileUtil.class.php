<?php
namespace ikarus\util;
use ikarus\system\Ikarus;
use ikarus\system\io\File;
use ikarus\system\io\ZipFile;
use ikarus\system\io\FTP;

/**
 * Provides methods for working with files
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class FileUtil {
	
	/**
	 * Returns the filepath for a temporary file
	 * @param 	string 		$prefix
	 * @param 	string 		$extension
	 * @param 	string		$dir
	 * @return 	string 				temporary filename
	 */
	public static function getTemporaryFilename($prefix = 'tmpFile_', $extension = '', $dir = null) {
		// get dir if needed
		if ($dir === null) $dir = static::getTemporaryDirname();
		
		// add trailing slash to dir
		$dir = self::addTrailingSlash($dir);
		
		do {
			$tmpFile = $dir.$prefix.StringUtil::getRandomID().$extension;
		} while (file_exists($tmpFile));

		return $tmpFile;
	}


	/**
	 * Removes a leading slash
	 * @param			string			$path
	 * @return			string
	 */
	public static function removeLeadingSlash($path) {
		if ($path{0} == '/') return substr($path, 1);
		return $path;
	}


	/**
	 * Removes a trailing slash
	 * @param			string			$path
	 * @return			string
	 */
	public static function removeTrailingSlash($path) {
		if (substr($path, -1) == '/') return substr($path, 0, -1);
		return $path;
	}


	/**
	 * Adds a trailing slash
	 * @param			string			$path
	 * @return			string
	 */
	public static function addTrailingSlash($path) {
		if (substr($path, -1) != '/') return $path.'/';
		return $path;
	}


	/**
	 * Builds a relative path from two absolute paths
	 * @param			string			$currentDir
	 * @param			string			$targetDir
	 * @return			string
	 */
	public static function getRelativePath($currentDir, $targetDir) {
		// remove trailing slashes
		$currentDir = static::removeTrailingSlash(static::unifyDirSeperator($currentDir));
		$targetDir = static::removeTrailingSlash(static::unifyDirSeperator($targetDir));

		// same dir?
		if ($currentDir == $targetDir) return './';

		// convert path to array
		$current = explode('/', $currentDir);
		$target = explode('/', $targetDir);

		// main action
		$relativePath = '';
		for ($i = 0, $max = max(count($current), count($target)); $i < $max; $i++)
			if (isset($current[$i]) and isset($target[$i])) {
				if ($current[$i] != $target[$i]) {
					for ($j = 0; $j < $i; $j++) {
						unset($target[$j]);
					}
					$relPath .= str_repeat('../', count($current) - $i).implode('/', $target).'/';
					for ($j = $i + 1; $j < count($current); $j++) {
						unset($current[$j]);
					}
					break;
				}
			} elseif (isset($current[$i]) && !isset($target[$i]))
				$relativePath .= '../';
			elseif (!isset($current[$i]) && isset($target[$i]))
				$relativePath .= $target[$i].'/';

		return $relativePath;
	}

	/**
	 * Unifies windows and unix directory seperators
	 * @param			string			$path
	 * @return			string
	 */
	public static function unifyDirSeperator($path) {
		$path = str_replace('\\\\', '/', $path);
		return str_replace('\\', '/', $path);
	}

	/**
	 * Returns canonicalized absolute pathname
	 * @param			string			$path
	 * @return			string
	 */
	public static function getRealPath($path) {
		// unify dir seperators
		$path = self::unifyDirSeperator($path);

		// create needed arrays
		$result = array();
		
		// split path to array
		$pathA = explode('/', $path);
		
		// support for linux' /
		if ($pathA[0] === '') $result[] = '';

		foreach ($pathA as $key => $dir)
			if ($dir == '..') {
				if (end($result) == '..')
					$result[] = '..';
				else {
					$lastValue = array_pop($result);
					if ($lastValue === '' || $lastValue === null) $result[] = '..';
				}
			} elseif ($dir !== '' && $dir != '.')
				$result[] = $dir;

		$lastValue = end($pathA);
		if ($lastValue === '' || $lastValue === false) $result[] = '';

		return implode('/', $result);
	}

	/**
	 * Formats a filesize
	 * @param			integer			$byte
	 * @param			integer			$precision
	 * @return			string
	 */
	public static function formatFilesize($byte, $precision = 2) {
		// start symbol
		$symbol = 'Byte';
		
		// kB
		if ($byte >= 1000) {
			$byte /= 1000;
			$symbol = 'kB';
		}
		
		// MB
		if ($byte >= 1000) {
			$byte /= 1000;
			$symbol = 'MB';
		}
		
		// GB
		if ($byte >= 1000) {
			$byte /= 1000;
			$symbol = 'GB';
		}
		
		// TB
		if ($byte >= 1000) {
			$byte /= 1000;
			$symbol = 'TB';
		}

		// format numeric
		return StringUtil::formatNumeric(round($byte, $precision)).' '.$symbol;
	}

	/**
	 * Formats a filesize (binary prefix)
	 * For more information: <http://en.wikipedia.org/wiki/Binary_prefix>
	 * @param			integer			$byte
	 * @param			integer			$precision
	 * @return			string
	 */
	public static function formatFilesizeBinary($byte, $precision = 2) {
		// start symbol
		$symbol = 'Byte';
		
		// KiB
		if ($byte >= 1024) {
			$byte /= 1024;
			$symbol = 'KiB';
		}
		
		// MiB
		if ($byte >= 1024) {
			$byte /= 1024;
			$symbol = 'MiB';
		}
		
		// GiB
		if ($byte >= 1024) {
			$byte /= 1024;
			$symbol = 'GiB';
		}
		
		// TiB
		if ($byte >= 1024) {
			$byte /= 1024;
			$symbol = 'TiB';
		}

		// format numeric
		return StringUtil::formatNumeric(round($byte, $precision)).' '.$symbol;
	}

	/**
	 * Downloads a package archive from an http URL
	 * @param	string		$httpUrl
	 * @param	string		$prefix
	 * @return	string		path to the dowloaded file
	 */
	public static function downloadFileFromHttp($httpUrl, $prefix = 'package') {
		// get filename
		$newFileName = self::getTemporaryFilename($prefix.'_');
		
		// open file handle
		$localFile = Ikarus::getFilesystemManager()->createFile($newFileName);

		// get proxy
		$options = array();
		if (Ikarus::getConfiguration()->get('global.advanced.httpProxy')) $options['http']['proxy'] = Ikarus::getConfiguration()->get('global.advanced.httpProxy');

		// first check for fopen() support
		if (function_exists('fopen') and ini_get('allow_url_fopen')) {
			// open file socket
			$remoteFile = new File($httpUrl, 'rb', $options);
			
			// get the content of the remote file and write it to a local file.
			while (!$remoteFile->eof()) {
				$buffer = $remoteFile->gets(4096);
				$localFile->append($buffer);
			}
		} else { // use own system
			$port = 80;
			$parsedUrl = parse_url($httpUrl);
			$host = $parsedUrl['host'];
			$path = $parsedUrl['path'];
			
			$remoteFile = new RemoteFile($host, $port, 30, $options); // the file to read.
			if (!isset($remoteFile)) throw new SystemException("cannot connect to http host '%s'", $host);
			
			// build and send the http request.
			$request = "GET ".$path.(!empty($parsedUrl['query']) ? '?'.$parsedUrl['query'] : '')." HTTP/1.0\r\n";
			$request .= "User-Agent: HTTP.PHP (FileUtil.class.php; Ikarus/".IKARUS_VERSION.";".(Ikarus::componentAbbreviationExists('LanguageManager') ? ' '.Ikarus::getLanguageManager()->getActiveLanguage()->getLanguageCode() : '').")\r\n";
			$request .= "Accept: */*\r\n";
			$request .= "Accept-Language: ".(Ikarus::componentAbbreviationExists('LanguageManager') ? Ikarus::getLanguageManager()->getActiveLanguage()->getLanguageCode() : 'en-US')."\r\n";
			$request .= "Host: ".$host."\r\n";
			$request .= "Connection: Close\r\n\r\n";
			$remoteFile->puts($request);
			$waiting = true;
			$readResponse = array();
			
			// read http response.
			while (!$remoteFile->eof()) {
				$readResponse[] = $remoteFile->gets();
				
				// look if we are done with transferring the requested file.
				if ($waiting)
					if (rtrim($readResponse[count($readResponse) - 1]) == '') $waiting = false;
				else {
					// look if the webserver sent an error http statuscode
					// This has still to be checked if really sufficient!
					$arrayHeader = array('201', '301', '302', '303', '307', '404');
					
					foreach ($arrayHeader as $code) {
						$error = strpos($readResponse[0], $code);
					}
					
					if ($error !== false) throw new SystemException("An error occoured while reading file %s at host %s", $path, $host);
					
					// write to the target system.
					$localFile->append($readResponse[count($readResponse) - 1]);
				}
			}
		}

		// close remote file
		$remoteFile->close();
		
		// write local file contents
		$localFile->write();
		
		// return filename
		return $newFileName;
	}

	/**
	 * Determines whether a file is text or binary by checking the first few bytes in the file.
	 * The exact number of bytes is system dependent, but it is typically several thousand.
	 * If every byte in that part of the file is non-null, considers the file to be text;
	 * otherwise it considers the file to be binary
	 * @todo Add support for filesystem wrapper
	 * @param			string			$file
	 * @return			boolean
	 * @deprecated
	 */
	public static function isBinary($file) {
		// open file
		$file = new File($file, 'rb');

		// get block size
		$stat = $file->stat();
		$blockSize = $stat['blksize'];
		if ($blockSize < 0) $blockSize = 1024;
		if ($blockSize > $file->filesize()) $blockSize = $file->filesize();
		if ($blockSize <= 0) return false;

		// get bytes
		$block = $file->read($blockSize);
		return (strlen($block) == 0 || preg_match_all('/\x00/', $block, $match) > 0);
	}

	/**
	 * Returns the value of the 'safe_mode' configuration option
	 * @return			boolean
	 */
	public static function getSafeMode() {
		// safe_mode was removed in php 5.4
		if (version_compare(PHP_VERSION, '5.4', '>=')) return false;
		
		// get from ini
		$configArray = @ini_get_all();
		if (is_array($configArray) && isset($configArray['safe_mode']['local_value'])) return intval($configArray['safe_mode']['local_value']);
		return intval(@ini_get('safe_mode'));
	}
}
?>