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

/**
 * Implements mathimatical functions which are not provided by PHP itself.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class MathUtil {

	/**
	 * @var                        number
	 */
	const BCCOMP_LARGER = 1;

	/**
	 * Converts binary data into a number.
	 * @param                        string $binaryData
	 * @return                        number
	 */
	public static function binaryToNumber ($data) {
		// init variables
		$base = 256;
		$radix = 1;
		$result = 0;

		// loop through bytes
		for ($i = strlen ($data) - 1; $i >= 0; $i--) {
			// extract digit
			$digit = ord ($data{$i});

			// calculate
			$part_res = bcmul ($digit, $radix);
			$result = bcadd ($result, $part_res);
			$radix = bcmul ($radix, $base);
		}

		// return result
		return $result;
	}

	/**
	 * Checks whether advanced mathematics are available on current PHP installation.
	 * @return                        boolean
	 */
	public static function isSupported () {
		return (extension_loaded ('bcmath'));
	}

	/**
	 * Converts a number to binary.
	 * @param                        integer $number
	 * @param                        integer $blocksize
	 * @return                        string
	 */
	public static function numberToBinary ($number, $blocksize) {
		// init variables
		$base = 256;
		$result = "";

		$div = $number;

		while ($div > 0) {
			// calculate
			$mod = bcmod ($div, $base);
			$div = bcdiv ($div, $base);

			// convert
			$result = chr ($mod) . $result;
		}

		// fill with 0 char
		return str_pad ($result, $blocksize, "\x00", STR_PAD_LEFT);
	}

	/**
	 * Calculates (p ^ q) mod r
	 * @param                number $p
	 * @param                number $q
	 * @param                number $r
	 * @return                number
	 */
	public static function powMod ($p, $q, $r) {
		// extract powers of 2 from q
		$factors = array();
		$div = $q;
		$powerOfTwo = 0;

		while (bccomp ($div, 0) == static::BCCOMP_LARGER) {
			$rem = bcmod ($div, 2);
			$div = bcdiv ($div, 2);

			if ($rem) array_push ($factors, $powerOfTwo);
			$powerOfTwo++;
		}

		// calculate partial results for each factor using each partial result as a starting point for the next
		// depends of the factors of two being generated in increasing order
		$partialResults = array();
		$partialResult = $p;
		$index = 0;

		foreach ($factors as $factor) {
			while ($index < $factor) {
				$partialResult = bcpow ($partialResult, 2);
				$partialResult = bcpow ($partialResult, $r);

				$index++;
			}

			array_push ($partialResults, $partialResult);
		}

		// calculate result
		$result = 1;

		foreach ($partialResults as $partialResult) {
			$result = bcmul ($result, $partialResult);
			$result = bcmod ($result, $r);
		}

		// everything done
		return $result;
	}
}

?>