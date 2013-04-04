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

/**
 * Represents a public key.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class RSAPublicKey extends IRSAKey {

	/**
	 * Stores the binary version of this key.
	 * @var			string
	 */
	protected $key = null;

	/**
	 * Stores the key length of this key.
	 * @var			integer
	 */
	protected $keyLength = 0;

	/**
	 * Stores the binary modulus of this key.
	 * @var			string
	 */
	protected $modulus = null;

	/**
	 * Constructs a new public key.
	 * @param			string			$key
	 * @param			string			$modulus
	 * @param			integer			$keyLength
	 */
	public function __construct($key, $modulus, $keyLength) {
		$this->key = $key;
		$this->modulus = $modulus;
		$this->keyLength = $keyLength;
	}

	/**
	 * @see ikarus\system\encryption\rsa\IRSAKey::decode()
	 * @throws			RSAKeyException
	 */
	public static function decode($data) {
		// decode string representation
		$data = base64_decode($data);

		// decode json
		$data = json_decode($data);

		// validate
		if (!isset($data->key)) throw new RSAKeyException('Invalid public key supplied: No key found');
		if (!isset($data->modulus))  throw new RSAKeyException('Invalid public key supplied: No modulus found');
		if (!isset($data->keyLength))  throw new RSAKeyException('Invalid public key supplied: No key length defined');

		// construct instance
		return (new static($data->key, $data->modulus, $data->keyLength));
	}

	/**
	 * @see ikarus\system\encryption\rsa\IRSAKey::getBinaryVersion()
	 */
	public function getBinaryVersion() {
		return $this->key;
	}

	/**
	 * @see ikarus\system\encryption\rsa\IRSAKey::getEncodedVersion()
	 */
	public function getEncodedVersion() {
		// init data holder
		$holder = new stdObject();

		// store data
		$holder->key = $this->key;
		$holder->modulus = $this->modulus;
		$holder->keyLength = $this->keyLength;

		// encode
		$data = json_encode($holder, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
		return base64_encode($data);
	}

	/**
	 * @see ikarus\system\encryption\rsa\IRSAKey::getType()
	 */
	public function getType() {
		return IRSAKey::TYPE_PUBLIC;
	}

	/**
	 * Verifies data against a signature.
	 * @return			boolean
	 */
	public function verify($data, $signature) {
		$rsa = new RSA($this->key, null, $this->modulus, $this->keyLength);
		$rsa->verify($data, $signature);
	}
}
?>