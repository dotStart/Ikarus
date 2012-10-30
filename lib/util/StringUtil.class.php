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
use ikarus\system\IKARUS;

/**
 * Contains string-related functions.
 *
 * @author 		Johannes Donath (originally written by Marcel Werk, WoltLab)
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class StringUtil {

	/**
	 * Contains a pattern that matches on HTML elements/documents
	 * @var			string
	 */
	const HTML_PATTERN = '~</?[a-z]+[1-6]?
			(?:\s*[a-z]+\s*=\s*(?:
			"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|[^\s>]
			))*\s*/?>~ix';

	/**
	 * Adds thousands separators to a given number
	 * @param		mixed			$number
	 * @return		string
	 */
	public static function addThousandsSeparator($number) {
		if ($number >= 1000 || $number <= -1000) $number = preg_replace('~(?<=\d)(?=(\d{3})+(?!\d))~', (Ikarus::componentAbbreviationExists('LanguageManager') ? Ikarus::getLanguageManager()->getActiveLanguage()->get('ikarus.global.decimalPoint') : '.'), $number);
		return $number;
	}
	
	/**
	 * @see substr_count()
	 */
	public static function countSubstring($hayStack, $needle) {
		if (static::mbStringAvailable())
			return mb_substr_count($hayStack, $needle);
		else
			return substr_count($hayStack, $needle);
	}
	
	/**
	 * Decodes html entities
	 * @param 		string 			$string
	 * @return 		string 			$string
	 */
	public static function decodeHTML($string) {
		if (is_object($string)) $string = (string) $string;
		$string = str_ireplace('&nbsp;', ' ', $string); // convert non-breaking spaces to ascii 32; not ascii 160
		return @html_entity_decode($string, ENT_COMPAT, defined('OPTION_CHARSET') ? CHARSET : 'UTF-8');
	}
	
	/**
	 * Converts html special characters
	 * @param 		string 			$string
	 * @return 		string 			$string
	 */
	public static function encodeHTML($string) {
		// convert to string
		if (is_object($string)) $string = (string) $string;
		return @htmlspecialchars($string, ENT_COMPAT, defined('OPTION_CHARSET') ? CHARSET : 'UTF-8');
	}
	
	/**
	 * Encodes a string as UTF8 (usually used for json_encode() stuff).
	 * @param			string			$string
	 * @return			string
	 */
	public static function encodeUTF8($string) {
		return utf8_encode($string);
	}
	
	/**
	 * @see ucfirst()
	 */
	public static function firstCharToUpperCase($string) {
		if (static::mbStringAvailable())
			return static::toUpperCase(static::substring($string, 0, 1)).static::substring($string, 1);
		else
			return ucfirst($string);
	}
	
	/**
	 * Formats a double
	 * @param		double			$double
	 * @param		integer			$minDecimals
	 * @return		string
	 */
	public static function formatDouble($double, $minDecimals = 0) {
		// consider as integer, if no decimal places found
		if (!$minDecimals && preg_match('~^(-?\d+)(?:\.(?:0*|00[0-4]\d*))?$~', $double, $match)) return static::formatInteger($match[1]);
	
		// round
		$double = round($double, ($minDecimals > 2 ? $minDecimals : 2));
	
		// remove last 0
		if ($minDecimals < 2 && substr($double, -1) == '0') $double = substr($double, 0, -1);
	
		// replace decimal point
		$double = str_replace('.', (Ikarus::componentAbbreviationExists('LanguageManager') ? Ikarus::getLanguageManager()->getActiveLanguage()->get('ikarus.global.decimalPoint') : '.'), $double);
	
		// add thousands separator
		return static::addThousandsSeparator($double);
	}
	
	/**
	 * Formats a numeric
	 * @param 		numeric 		$numeric
	 * @return 		string
	 */
	public static function formatNumeric($numeric) {
		if (is_int($numeric))
			return static::formatInteger($numeric);
		else if (is_float($numeric))
			return static::formatDouble($numeric);
		else
			if (floatval($numeric) - (float) intval($numeric))
			return static::formatDouble($numeric);
		else
			return static::formatInteger(intval($numeric));
	}
	
	/**
	 * Returns a double salted hash of the given value
	 * @param 		string			$value
	 * @param		string			$salt
	 * @return 		string	 		$hash
	 */
	public static function getDoubleSaltedHash($value, $salt) {
		return static::getSaltedHash(static::getSaltedHash($value, $salt), $salt, true);
	}
	
	/**
	 * Creates a random hash
	 * @return		string
	 */
	public static function getRandomID() {
		return EncryptionManager::hash(microtime() . uniqid(mt_rand(), true));
	}
	
	/**
	 * Returns a salted hash of the given value
	 * @param 		string			$value
	 * @param		string			$salt
	 * @return 		string			$hash
	 */
	public static function getSaltedHash($value, $salt, $prepend = false) {
		return EncryptionManager::hash(($prepend ? $salt : '').$value.(!$prepend ? $salt : ''));
	}
	
	/**
	 * @see strpos()
	 */
	public static function indexOf($hayStack, $needle, $offset = 0) {
		if (static::mbStringAvailable())
			return mb_strpos($hayStack, $needle, $offset);
		else
			return strpos($hayStack, $needle, $offset);
	}
	
	/**
	 * @see stripos()
	 */
	public static function indexOfIgnoreCase($hayStack, $needle, $offset = 0) {
		if (static::mbStringAvailable())
			return mb_strpos(static::toLowerCase($hayStack), static::toLowerCase($needle), $offset);
		else
			return stripos($hayStack, $needle, $offset);
	}
	
	/**
	 * @see strrpos()
	 */
	public static function lastIndexOf($hayStack, $needle) {
		if (static::mbStringAvailable())
			return mb_strrpos($hayStack, $needle);
		else
			return strrpos($hayStack, $needle);
	}
	
	/**
	 * @see strlen()
	 */
	public static function length($string) {
		if (static::mbStringAvailable())
			return mb_strlen($string);
		else
			return strlen($string);
	}
	
	/**
	 * Checks whether mbstring is available.
	 * @return boolean
	 */
	protected static function mbStringAvailable() {
		return extension_loaded('mbstring');
	}
	
	/**
	 * @see str_replace()
	 */
	public static function replace($search, $replace, $subject, &$count = null) {
		return str_replace($search, $replace, $subject, $count);
	}
	
	/**
	 * @see str_ireplace()
	 */
	public static function replaceIgnoreCase($search, $replace, $subject, &$count = 0) {
		if (static::mbStringAvailable()) {
			$startPos = static::indexOf(static::toLowerCase($subject), static::toLowerCase($search));
			if ($startPos === false)
				return $subject;
			else {
				$endPos = $startPos + static::length($search);
				$count++;
				return static::substring($subject, 0, $startPos) . $replace . static::replaceIgnoreCase($search, $replace, static::substring($subject, $endPos), $count);
			}
		} else
			return str_ireplace($search, $replace, $subject, $count);
	}
	
	/**
	 * Sorts an array of strings and maintain index association
	 * @param 		array			$strings
	 * @return 		boolean
	 */
	public static function sort(&$strings) {
		return asort($strings, SORT_LOCALE_STRING);
	}
	
	/**
	 * Strips HTML tags from a string
	 * @param		string			$string
	 * @return		string
	 */
	public static function stripHTML($string) {
		return preg_replace(static::HTML_PATTERN, '', $string);
	}
	
	/**
	 * @see substr()
	 */
	public static function substring($string, $start, $length = null) {
		if (static::mbStringAvailable()) {
			if ($length !== null) return mb_substr($string, $start, $length);
			return mb_substr($string, $start);
		} else {
			if ($length !== null) return substr($string, $start, $length);
			return substr($string, $start);
		}
	}

	/**
	 * @see strtolower()
	 */
	public static function toLowerCase($string) {
		if (static::mbStringAvailable())
			return mb_strtolower($string);
		else
			return strtolower($string);
	}
	
	/**
	 * @see strtoupper()
	 */
	public static function toUpperCase($string) {
		if (static::mbStringAvailable())
			return mb_strtoupper($string);
		else
			return strtoupper($string);
	}
	
	/**
	 * @see trim()
	 */
	public static function trim($text) {
		return trim($text);
	}
	
	/**
	 * Unescapes escaped characters in a string
	 * @param		string			$string
	 * @param		string			$chars
	 * @return 		string
	 */
	public static function unescape($string, $chars = '"') {
		for ($i = 0, $j = strlen($chars); $i < $j; $i++) $string = static::replace('\\'.$chars[$i], $chars[$i], $string);
		return $string;
	}
	
	/**
	 * Converts dos to unix newlines
	 * @param 		string 			$string
	 * @return 		string 			$string
	 */
	public static function unifyNewlines($string) {
		return preg_replace("~(\r\n)|(\r)~", "\n", $string);
	}

	/**
	 * @see ucwords()
	 */
	public static function wordsToUpperCase($string) {
		if (static::mbStringAvailable())
			return mb_convert_case($string, MB_CASE_TITLE);
		else
			return ucwords($string);
	}
}
?>