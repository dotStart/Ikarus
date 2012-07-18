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
	 * Returns a salted hash of the given value
	 * @param 		string			$value
	 * @param		string			$salt
	 * @return 		string			$hash
	 */
	public static function getSaltedHash($value, $salt) {
		return EncryptionManager::hash($value.$salt);
	}

	/**
	 * Returns a double salted hash of the given value
	 * @param 		string			$value
	 * @param		string			$salt
	 * @return 		string	 		$hash
	 */
	public static function getDoubleSaltedHash($value, $salt) {
		return EncryptionManager::hash($salt . static::getSaltedHash($value, $salt));
	}

	/**
	 * Creates a random hash
	 * @return		string
	 */
	public static function getRandomID() {
		return EncryptionManager::hash(microtime() . uniqid(mt_rand(), true));
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
	 * @see trim()
	 */
	public static function trim($text) {
		return trim($text);
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
	 * Formats an integer
	 * @param		integer			$integer
	 * @return		string
	 */
	public static function formatInteger($integer) {
		return static::addThousandsSeparator($integer);
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
	 * Adds thousands separators to a given number
	 * @param		mixed			$number
	 * @return		string
	 */
	public static function addThousandsSeparator($number) {
		if ($number >= 1000 || $number <= -1000) $number = preg_replace('~(?<=\d)(?=(\d{3})+(?!\d))~', (Ikarus::componentAbbreviationExists('LanguageManager') ? Ikarus::getLanguageManager()->getActiveLanguage()->get('ikarus.global.decimalPoint') : '.'), $number);
		return $number;
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
	 * @see strlen()
	 */
	public static function length($string) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strlen($string);
		else
			return strlen($string);
	}

	/**
	 * @see strpos()
	 */
	public static function indexOf($hayStack, $needle, $offset = 0) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strpos($hayStack, $needle, $offset);
		else
			return strpos($hayStack, $needle, $offset);
	}

	/**
	 * @see stripos()
	 */
	public static function indexOfIgnoreCase($hayStack, $needle, $offset = 0) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strpos(static::toLowerCase($hayStack), static::toLowerCase($needle), $offset);
		else
			return stripos($hayStack, $needle, $offset);
	}

	/**
	 * @see strrpos()
	 */
	public static function lastIndexOf($hayStack, $needle) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strrpos($hayStack, $needle);
		else
			return strrpos($hayStack, $needle);
	}

	/**
	 * @see substr()
	 */
	public static function substring($string, $start, $length = null) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString')) {
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
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strtolower($string);
		else
			return strtolower($string);
	}

	/**
	 * @see strtoupper()
	 */
	public static function toUpperCase($string) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_strtoupper($string);
		else
			return strtoupper($string);
	}

	/**
	 * @see substr_count()
	 */
	public static function countSubstring($hayStack, $needle) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_substr_count($hayStack, $needle);
		else
			return substr_count($hayStack, $needle);
	}

	/**
	 * @see ucfirst()
	 */
	public static function firstCharToUpperCase($string) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return static::toUpperCase(static::substring($string, 0, 1)).static::substring($string, 1);
		else
			return ucfirst($string);
	}

	/**
	 * @see ucwords()
	 */
	public static function wordsToUpperCase($string) {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_convert_case($string, MB_CASE_TITLE);
		else
			return ucwords($string);
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
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString')) {
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
	 * Takes a numeric HTML entity value and returns the appropriate UTF-8 bytes
	 * @param		integer			$dec		html entity value
	 * @return		string
	 */
	public static function getCharacter($dec) {
		if ($dec < 128)
			$utf = chr($dec);
		else if ($dec < 2048) {
			$utf = chr(192 + (($dec - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		} else {
			$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
			$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		}
		return $utf;
	}

	/**
	 * Converts UTF-8 to Unicode
	 * @see http://www1.tip.nl/~t876506/utf8tbl.html
	 * @param		string			$c
	 * @return		integer
	 */
	public static function getCharValue($c) {
		$ud = 0;
		if (ord($c{0}) >= 0 && ord($c{0}) <= 127) $ud = ord($c{0});
		if (ord($c{0}) >= 192 && ord($c{0}) <= 223) $ud = (ord($c{0}) - 192) * 64 + (ord($c{1}) - 128);
		if (ord($c{0}) >= 224 && ord($c{0}) <= 239) $ud = (ord($c{0}) - 224) * 4096 + (ord($c{1}) - 128) * 64 + (ord($c{2}) - 128);
		if (ord($c{0}) >= 240 && ord($c{0}) <= 247) $ud = (ord($c{0}) - 240) * 262144 + (ord($c{1}) - 128) * 4096 + (ord($c{2}) - 128) * 64 + (ord($c{3}) - 128);
		if (ord($c{0}) >= 248 && ord($c{0}) <= 251) $ud = (ord($c{0}) - 248) * 16777216 + (ord($c{1}) - 128) * 262144 + (ord($c{2}) - 128) * 4096 + (ord($c{3}) - 128) * 64 + (ord($c{4}) - 128);
		if (ord($c{0}) >= 252 && ord($c{0}) <= 253) $ud = (ord($c{0}) - 252) * 1073741824 + (ord($c{1}) - 128) * 16777216 + (ord($c{2}) - 128) * 262144 + (ord($c{3}) - 128) * 4096 + (ord($c{4}) - 128) * 64 + (ord($c{5}) - 128);
		if (ord($c{0}) >= 254 && ord($c{0}) <= 255) $ud = false; // error
		return $ud;
	}

	/**
	 * Returns html entities of all characters in the given string
	 * @param		string			$string
	 * @return		string
	 */
	public static function encodeAllChars($string) {
		$result = '';
		for ($i = 0, $j = StringUtil::length($string); $i < $j; $i++) {
			$char = StringUtil::substring($string, $i, 1);
			$result .= '&#'.(Ikarus::getConfiguration()->get('global.advanced.useMBString') ? StringUtil::getCharValue($char) : ord($char)).';';
		}
		return $result;
	}

	/**
	 * Returns true, if the given string contains only ASCII characters
	 * @param		string			$string
	 * @return		boolean
	 */
	public static function isASCII($string) {
		return preg_match('/^[\x00-\x7F]*$/', $string);
	}

	/**
	 * Returns true, if the given string is utf-8 encoded
	 * @see http://www.w3.org/International/questions/qa-forms-utf-8
	 * @param		string			$string
	 * @return		boolean
	 */
	public static function isUTF8($string) {
		return preg_match('/(
				[\xC2-\xDF][\x80-\xBF]			# non-overlong 2-byte
			|	\xE0[\xA0-\xBF][\x80-\xBF]		# excluding overlongs
			|	[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
			|	\xED[\x80-\x9F][\x80-\xBF]		# excluding surrogates
			|	\xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
			|	[\xF1-\xF3][\x80-\xBF]{3}		# planes 4-15
			|	\xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
			)/x', $string);
	}

	/**
	 * Extracts the class name from a standardised class path
	 * @param		string			$classPath
	 * @return		string
	 */
	public static function getClassName($classPath) {
		return preg_replace('~(?:.*/)?([^/]+).class.php~i', '\\1', $classPath);
	}

	/**
	 * Escapes the closing cdata tag
	 * @param		string			$string
	 * @return		string
	 */
	public static function escapeCDATA($string) {
		return str_replace(']]>', ']]]]><![CDATA[>', $string);
	}

	/**
	 * Converts a string to requested character encoding
	 * @see mb_convert_encoding()
	 * @param 		string			$inCharset
	 * @param 		string			$outCharset
	 * @param 		string			$string
	 * @return 		string
	 */
	public static function convertEncoding($inCharset, $outCharset, $string) {
		if ($inCharset == 'ISO-8859-1' && $outCharset == 'UTF-8') return utf8_encode($string);
		if ($inCharset == 'UTF-8' && $outCharset == 'ISO-8859-1') return utf8_decode($string);

		//return iconv($inCharset, $outCharset, $string);
		return mb_convert_encoding($string, $outCharset, $inCharset);
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
	 * Returns false, if the given word is forbidden by given word filter
	 * @param 		string			$word
	 * @param		string			$filter
	 * @return		boolean
	 */
	public static function executeWordFilter($word, $filter) {
		if ($filter == '') return true;
		
		$word = static::toLowerCase($word);

		if ($filter != '') {
			$forbiddenNames = explode("\n", static::toLowerCase(static::unifyNewlines($filter)));
			
			foreach ($forbiddenNames as $forbiddenName)
				if (static::indexOf($forbiddenName, '*') !== false) {
					$forbiddenName = static::replace('\*', '.*', preg_quote($forbiddenName, '/'));
					if (preg_match('/^'.$forbiddenName.'$/s', $word)) return false;
				} elseif ($word == $forbiddenName) return false;
		}

		return true;
	}

	/**
	 * Splits given string into smaller chunks
	 * @param		string			$string
	 * @param		integer			$length
	 * @param		string			$break
	 * @return		string
	 */
	public static function splitIntoChunks($string, $length = 75, $break = "\r\n") {
		if (Ikarus::getConfiguration()->get('global.advanced.useMBString'))
			return mb_ereg_replace('.{'.$length.'}', "\\0".$break, $string);
		else
			return chunk_split($string, $length, $break);
	}
}
?>