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
 * Provides util methods for arrays
 * @author		Johannes Donath (originally written by Marcel Werk, WoltLab)
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ArrayUtil {
	
	/**
	 * Appends a suffix to all elements of the given array.
	 * @param			array			$array
	 * @param			string			$suffix
	 * @return			array
	 */
	public static function appendSuffix($array, $suffix) {
		return array_map(function($element) {
			return $element.$suffix;
		}, $array);
	}
	
	/**
	 * Converts a array of strings to requested character encoding.
	 * Note: This method is recursive
	 * @see ikarus\util\StringUtil::convertEncoding
	 * @param			string			$inCharset
	 * @param			string			$outCharset
	 * @param			string			$array
	 * @return			string
	 */
	public static function convertEncoding($inCharset, $outCharset, $array) {
		if (!is_array($array))
			return StringUtil::convertEncoding($inCharset, $outCharset, $array);
		else
			return array_map(function($element) {
			return static::convertEncoding($inCharset, $outCharset, $element);
		}, $array);
	}
	
	/**
	 * Converts html special characters in arrays.
	 * Note: This method is recursive
	 * @param			array			$array
	 * @return			array
	 */
	public static function encodeHTML($array) {
		if (!is_array($array))
			return StringUtil::encodeHTML($array);
		else
			return array_map(array('static', 'encodeHTML'), $array);
	}
	
	/**
	 * Searches for a part of $search in $array
	 * @param			array			$array
	 * @param			array			$search
	 * @return			boolean
	 * @todo Check for a better way to do this.
	 */
	public static function in_array($array, $search) {
		foreach($array as $val) {
			if (in_array($val, $search)) return true;
		}
		return false;
	}
	
	/**
	 * Applies stripslashes on all elements of an array.
	 * Note: This method is recursive
	 * @param			array			$array
	 * @return			array
	 */
	public static function stripslashes($array) {
		if (!is_array($array))
			return stripslashes($array);
		else
			return array_map(array('static', 'stripslashes'), $array);
	}
	
	/**
	 * Applies intval() to all elements of an array.
	 * Note: This method is recursive
	 * @param			array			$array
	 * @return			array
	 */
	public static function toIntegerArray($array) {
		if (!is_array($array))
			return intval($array);
		else
			return array_map(array('static', 'toIntegerArray'), $array);
	}
	
	/**
	 * Applies StringUtil::trim() to all elements of an array.
	 * Note: This method is recursive
	 * @param			array			$array
	 * @param			boolean			$removeEmptyElements
	 * @return			array 
	 */
	public static function trim($array, $removeEmptyElements = true) {
		if (!is_array($array))
			return StringUtil::trim($array);
		else {
			$array = array_map(array('static', 'trim'), $array);
			if ($removeEmptyElements) foreach($array as $key => $value) if (empty($value)) unset($array[$key]);
			return $array;
		}
	}

	/**
	 * Converts dos to unix newlines.
	 * Note: This method is recursive
	 * @param			array			$array
	 * @return			array
	 */
	public static function unifyNewlines($array) {
		if (!is_array($array))
			return StringUtil::unifyNewlines($array);
		else
			return array_map(array('static', 'unifyNewlines'), $array);
	}
}
?>