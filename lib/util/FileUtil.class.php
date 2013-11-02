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
namespace ikarus\util;

use ikarus\system\Ikarus;

/**
 * Provides methods for working with files
 * @author                    Johannes Donath (originally written by Marcel Werk, WoltLab)
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class FileUtil {

	/**
	 * Stores a suffix for all temporary files.
	 * @var                        string
	 */
	const TEMPORARY_SUFFIX = '.tmp';

	/**
	 * Alias for dirname().
	 * @see                        dirname()
	 * @api
	 */
	public static function getDirectoryName ($path) {
		return dirname ($path);
	}

	/**
	 * Alias for basename().
	 * @see                        basename()
	 * @api
	 */
	public static function getFilename ($path, $suffix = '') {
		return basename ($path, $suffix);
	}

	/**
	 * Returns the path of a temporary file.
	 * @param                        ikarus\system\io\adapter\IFilesystemAdapter $adapter
	 * @return                        string
	 * @api
	 */
	public static function getTemporaryFilename (ikarus\system\io\adapter\IFilesystemAdapter $adapter = null) {
		// get adapter if needed
		if ($adapter === null) $adapter = Ikarus::getFilesystemManager ()->getDefaultAdapter ();

		// get directory
		$directory = $adapter->getTemporaryDirectory ();

		do {
			$tmpFile = $directory . StringUtil::getRandomID () . static::TEMPORARY_SUFFIX;
		} while ($adapter->fileExists ($tmpFile));

		// return temporary filename
		return $tmpFile;
	}


	/**
	 * Removes a leading slash.
	 * @param                        string $path
	 * @return                        string
	 * @api
	 */
	public static function removeLeadingSlash ($path) {
		if ($path{0} == '/') return substr ($path, 1);

		return $path;
	}


	/**
	 * Removes a trailing slash
	 * @param                        string $path
	 * @return                        string
	 * @api
	 */
	public static function removeTrailingSlash ($path) {
		if (substr ($path, -1) == '/') return substr ($path, 0, -1);

		return $path;
	}


	/**
	 * Adds a trailing slash
	 * @param                        string $path
	 * @return                        string
	 * @api
	 */
	public static function addTrailingSlash ($path) {
		if (substr ($path, -1) != '/') return $path . '/';

		return $path;
	}


	/**
	 * Builds a relative path from two absolute paths
	 * @param                        string $currentDir
	 * @param                        string $targetDir
	 * @return                        string
	 * @api
	 */
	public static function getRelativePath ($currentDir, $targetDir) {
		// remove trailing slashes
		$currentDir = static::removeTrailingSlash (static::unifyDirSeperator ($currentDir));
		$targetDir = static::removeTrailingSlash (static::unifyDirSeperator ($targetDir));

		// same dir?
		if ($currentDir == $targetDir) return './';

		// convert path to array
		$current = explode ('/', $currentDir);
		$target = explode ('/', $targetDir);

		// main action
		$relativePath = '';
		for ($i = 0, $max = max (count ($current), count ($target)); $i < $max; $i++) {
			if (isset($current[$i]) and isset($target[$i])) {
				if ($current[$i] != $target[$i]) {
					for ($j = 0; $j < $i; $j++) {
						unset($target[$j]);
					}
					$relPath .= str_repeat ('../', count ($current) - $i) . implode ('/', $target) . '/';
					for ($j = $i + 1; $j < count ($current); $j++) {
						unset($current[$j]);
					}
					break;
				}
			} elseif (isset($current[$i]) && !isset($target[$i])) $relativePath .= '../';
			elseif (!isset($current[$i]) && isset($target[$i])) $relativePath .= $target[$i] . '/';
		}

		return $relativePath;
	}

	/**
	 * Unifies windows and unix directory seperators
	 * @param                        string $path
	 * @return                        string
	 * @api
	 */
	public static function unifyDirSeperator ($path) {
		$path = str_replace ('\\\\', '/', $path);

		return str_replace ('\\', '/', $path);
	}

	/**
	 * Returns canonicalized absolute pathname
	 * @param                        string $path
	 * @return                        string
	 * @api
	 */
	public static function getRealPath ($path) {
		// unify dir seperators
		$path = static::unifyDirSeperator ($path);

		// create needed arrays
		$result = array();

		// split path to array
		$pathA = explode ('/', $path);

		// support for linux' /
		if ($pathA[0] === '') $result[] = '';

		foreach ($pathA as $key => $dir) {
			if ($dir == '..') {
				if (end ($result) == '..') $result[] = '..'; else {
					$lastValue = array_pop ($result);
					if ($lastValue === '' || $lastValue === null) $result[] = '..';
				}
			} elseif ($dir !== '' && $dir != '.') $result[] = $dir;
		}

		$lastValue = end ($pathA);
		if ($lastValue === '' || $lastValue === false) $result[] = '';

		return implode ('/', $result);
	}

	/**
	 * Formats a filesize
	 * @param                        integer $byte
	 * @param                        integer $precision
	 * @return                        string
	 * @api
	 */
	public static function formatFilesize ($byte, $precision = 2) {
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
		return StringUtil::formatNumeric (round ($byte, $precision)) . ' ' . $symbol;
	}

	/**
	 * Formats a filesize (binary prefix)
	 * For more information: <http://en.wikipedia.org/wiki/Binary_prefix>
	 * @param                        integer $byte
	 * @param                        integer $precision
	 * @return                        string
	 * @api
	 */
	public static function formatFilesizeBinary ($byte, $precision = 2) {
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
		return StringUtil::formatNumeric (round ($byte, $precision)) . ' ' . $symbol;
	}
}

?>