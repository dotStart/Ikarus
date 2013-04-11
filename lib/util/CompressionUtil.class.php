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
namespace ikarus\util;

/**
 * Provides a compression API.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CompressionUtil {

	/**
	 * Contains a default level for compressions.
	 */
	const DEFAULT_LEVEL = 2;

	/**
	 * Compresses data.
	 * @param			string			$data
	 * @param			integer			$level			You should leave this at it's default level (2)
	 * @throws			CompressionException
	 * @return			string
	 * @api
	 */
	public static function compress($data, $level = static::DEFAULT_LEVEL) {
		// check support
		if (!static::isSupported()) throw new CompressionException("Cannot compress data: zlib is not available");

		// compress data
		$result = gzencode($data, $level, FORCE_GZIP);

		// error
		if ($result === false) throw new CompressionException("Cannot compress data: An error occured while compressing");

		// all ok
		return $result;
	}

	/**
	 * Decompresses data.
	 * @param			string			$data
	 * @throws			CompressionException
	 * @return			string
	 * @api
	 */
	public static function decompress($data) {
		// check support
		if (!static::isSupported()) throw new CompressionException("Cannot compress data: zlib is not available");

		// decompress data
		$result = gzdecode($data);

		// error
		if ($result === false) throw new CompressionException("Cannot decompress data: An error occured while decompressing");

		// all ok
		return $result;
	}
	
	/**
	 * Decompresses data with zlibs deflate algorithm.
	 * @param			string			$data
	 * @throws			CompressionException
	 * @return			string
	 * @api
	 */
	public static function deflate($data) {
		// check support
		if (!static::isSupported()) throw new CompressionException("Cannot compress data: zlib is not available");
		
		// decompress data
		$result = gzdeflate($data);
		
		// error
		if ($result === false) throw new CompressionException("Cannot decompress data: An error occured while decompressing");
		
		// all ok
		return $result;
	}

	/**
	 * Checks whether compression via zlib is supported.
	 * @return			boolean
	 * @api
	 */
	public static function isSupported() {
		return extension_loaded('zlib');
	}
}
?>