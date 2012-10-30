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
 * Provides wrapper methods for arrays.
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ArrayUtil {
	
	/**
	 * Appends a suffix to a stack of strings (Non-string elements will be ignored).
	 * @param			array			$array
	 * @param			string			$suffix
	 * @return			array
	 */
	public static function appendSuffix($array, $suffix) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		return array_map(function($element) {
			if (!is_string($element)) return $element;
			return $element.$suffix;
		}, $array);
	}
	
	/**
	 * Converts all non-html conform chars into HTML compatible strings (Non-string elements will be ignored).
	 * @param			array			$array
	 * @return			array
	 */
	public static function encodeHTML($array) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		return array_map(function($element) {
			if (is_array($element))
				return static::encodeHTML($element);
			elseif (is_string($element))
				return StringUtil::encodeHTML($element);
		}, $array);
	}
	
	/**
	 * Encodes a string stack into UTF8 (useful for json_encode()).
	 * @param			array			$array
	 * @return			array
	 * @throws			StrictStandardException
	 */
	public static function encodeUTF8($array) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		return array_map(function($element) {
			if (is_array($element))
				return static::encodeUTF8($element);
			elseif (is_string($element))
				return StringUtil::encodeUTF8($element);
		}, $array);
	}
	
	/**
	 * Searches for a string in the whole stack (recursive).
	 * @param			array			$array
	 * @param			array			$search
	 * @return			boolean
	 * @todo Check for a better way to do this.
	 */
	public static function in_array($array, $search) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		foreach($array as $val) {
			if (in_array($val, $search)) return true;
		}
		return false;
	}
	
	/**
	 * Converts all elements (!) into integers.
	 * @param			array			$array
	 * @return			array
	 */
	public static function toIntegerArray($array) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		return array_map(function($element) {
			if (is_array($element))
				return static::toIntegerArray($element);
			elseif (!is_integer($element))
				return intval($element); // TODO: We should create a util for such methods.
		}, $array);
	}
	
	/**
	 * Trims all elements of a string stack (Non-string elements will be ignored).
	 * @param			array			$array
	 * @param			boolean			$removeEmptyElements
	 * @return			array 
	 */
	public static function trim($array, $removeEmptyElements = true) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		$array = array_map(function($element) {
			if (is_array($element))
				return static::trim($element);
			elseif (is_string($element))
				return StringUtil::trim($element);
		}, $array);
		
		// delete "" (0) strings (empty)
		if ($removeEmptyElements)
			foreach($array as $key => $value)
				if (empty($value)) unset($array[$key]);
		
		// all done
		return $array;
	}

	/**
	 * Replaces all non-*NIX newlines with *NIX newlines (Non-string elements will be ignored).
	 * @param			array			$array
	 * @return			array
	 */
	public static function unifyNewlines($array) {
		if (!is_array($array)) throw new StrictStandardException(__CLASS__.'::'.__FUNCTION__.' expects parameter 1 to be array');
		
		return array_map(function($element) {
			if (is_array($element))
				return static::unifyNewlines($element);
			elseif (is_string($element))
				return StringUtil::unifyNewlines($element);
		}, $array);
	}
}
?>