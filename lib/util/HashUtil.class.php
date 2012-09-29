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
 * Allows hashing of strings.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class HashUtil {
	
	/**
	 * Defines the used checksum algorithm.
	 * @var			string
	 */
	const CHECKSUM_ALGORITHM = 'sha256';
	
	/**
	 * Defines the used hash algorithm.
	 * @var			string
	 */
	const HASH_ALGORITHM = 'sha256';
	
	/**
	 * Generates a checksum.
	 * @param			string			$fileContents
	 * @return			string
	 */
	public static function generateChecksum($fileContents) {
		return hash(static::CHECKSUM_ALGORITHM, $fileContents);
	}
	
	/**
	 * Hashes a string.
	 * @param			string			$str
	 * @return			string
	 */
	public static function hash($str) {
		return hash(static::HASH_ALGORITHM, $str);
	}
}
?>