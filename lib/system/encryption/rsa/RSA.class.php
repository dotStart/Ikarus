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
namespace ikarus\system\encryption\rsa;
use ikarus\util\MathUtil;

/**
 * A pure PHP implementation of the RSA algorithm.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class RSA {

	/**
	 * Defines the default key length.
	 * @var			integer
	 */
	const DEFAULT_KEY_LENGTH = 1024;

	/**
	 * Stores the hash algorithm to use.
	 * @var			string
	 */
	const HASH_ALGORITHM = 'sha512';

	/**
	 * Defines the current algorithm version.
	 * @var			string
	 */
	const VERSION = '0.10';

	/**
	 * Stores the encryption key's length.
	 * @var			integer
	 */
	protected $keyLength = 0;

	/**
	 * Stores the RSA modulus.
	 * @var			integer
	 */
	protected $modulus = null;

	/**
	 * Stores the private key.
	 * @var			integer
	 */
	protected $privateKey = null;

	/**
	 * Stores the public key.
	 * @var			integer
	 */
	protected $publicKey = null;

	/**
	 * Constructs the RSA algorithm.
	 * @param			string			$publicKey
	 * @param			string			$privateKey
	 * @param			string			$modulus
	 * @param			integer			$keyLength
	 */
	public function __construct($publicKey, $privateKey, $modulus, $keyLength) {
		// decode and store keys
		$this->publicKey = MathUtil::binaryToNumber($publicKey);
		$this->privateKey = MathUtil::binaryToNumber($privateKey);
		$this->modulus = MathUtil::binaryToNumber($modulus);
		$this->keyLength = $keyLength;
	}

	/**
	 * Adds a padding to a decrypted string.
	 * @param			string			$data
	 * @param			integer			$blocksize
	 * @param			boolean			$isPublicKey
	 * @return			string
	 */
	public static function addPKCS1Padding($data, $blocksize, $isPublicKey = true) {
		// calculate padding
		$paddingLength = ($blocksize - 3 - strlen($data));

		// init variables
		$blockType = "\x01"; // Note: By default this is set to the private key type
		$padding = '';

		// different padding for public & private
		if($isPublicKey) {
			// set block type
			$blockType = "\x02";

			// append random data
			for($i = 0; $i < $paddingLength; $i++) {
				$padding .= chr(mt_rand(1, 255));
			}
		} else {
			// append character 255
			$padding = str_repeat("\xFF", $paddingLength);
		}

		// return result
		return "\x00".$blockType.$padding."\x00".$data;
	}

	/**
	 * Checks whether key generation is supported on this system.
	 * @return			boolean
	 */
	public static function isGenerationSupported() {
		return (extension_loaded('openssl'));
	}

	/**
	 * Checks whether RSA is supported on this system.
	 * @return			boolean
	 */
	public static function isSupported() {
		return (MathUtil::isSupported());
	}

	/**
	 * Removes a padding from a string.
	 * @param			string			$data
	 * @param			integer			$blocksize
	 * @throws			RSAException
	 * @return			string
	 */
	public static function removePKCS1Padding($data, $blocksize) {
		// validity check
		if (strlen($data) == $blocksize) throw new RSAException('Blocksize cannot be as same as big as data amount');

		// remove prefix
		$data = substr($data, 1);

		// We cannot deal with block type 0
		if($data{0} == '\0') throw new RSAException('Block type 0 is not implemented'); // XXX: TODO!!!

		// block type can be 1 or 2
		if ($data{0} != "\x01" and $data{0} != "\x02") throw new RSAException('Invalid block type supplied');

		// calculate offset
		$offset = strpos($data, "\0", 1);

		// remove padding
		return substr($data, ($offset + 1));
	}

	/**
	 * Calculates a signature for given data with specified private key.
	 * @param			string			$data
	 * @return			string
	 */
	public function sign($data) {
		// generate hash
		$hash = hash(static::HASH_ALGORITHM, $data, true); // XXX: Replace against hash manager

		// add padding
		$data = static::addPKCS1Padding($hash, false, ($this->keyLength / 8));

		// convert to number
		$data = MathUtil::binaryToNumber($data);

		// sign
		$signed = MathUtil::powMod($data, $this->privateKey, $this->modulus);

		// convert back
		return MathUtil::numberToBinary($signed, ($this->keyLength / 8));
	}

	/**
	 * Verifies data against a signature.
	 * @param			string			$data
	 * @param			string			$signature
	 * @return			boolean
	 */
	public function verify($data, $signature) {
		// decode
		$data = MathUtil::binaryToNumber($data);

		// decrypt
		$data = MathUtil::powMod($number, $this->publicKey, $this->modulus);

		// calculate signed hash
		$signedHash = $this->removePKCS1Padding($data, ($this->keyLength / 8));

		// calculate current data hash
		$hash = hash(static::HASH_ALGORITHM, $data, true);

		// compare
		return (strcasecmp($signedHash, $hash) == 0);
	}
}
?>