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
use ikarus\system\exception\encryption\EncryptionException;
use ikarus\system\exception\encryption\EncryptionFailedException;

/**
 * Manages encryptions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class EncryptionManager {
	
	/**
	 * Contains the name of the algorithm that should used for en/decryption
	 * @var				string
	 */
	const CRYPT_ALGORITHM = 'rijndael-256';
	
	/**
	 * Contains the mode for en/decryption
	 * @var				string
	 */
	const CRYPT_MODE = 'ctr';
	
	/**
	 * Contains the name of the algorithm used for hashes
	 * @var				string
	 */
	const HASH_ALGORITHM = MHASH_SHA512;
	
	/**
	 * Creates an initialization vector
	 * @return			string
	 */
	public static function createIV() {
		return mcrypt_create_iv(mcrypt_get_iv_size(static::CRYPT_ALGORITHM, static::CRYPT_MODE), MCRYPT_RAND);
	}
	
	/**
	 * Decrypts given string
	 * @param			string			$key
	 * @param			string			$data
	 * @param			string			$iv
	 * @throws			SystemException
	 * @return			string
	 */
	public static function decrypt($key, $data, $iv) {
		if (!static::encryptionAvailable()) throw new EncryptionException("Encryption is not supported by php installation");
		
		// open handle
		$handle = static::getEncryptionHandle();
		
		// init mcrypt
		if (mcrypt_generic_init($handle, $key, $iv) !== 0) throw new EncryptionException("Initialization of mcrypt failed");
		
		return mdecrypt_generic($handle, $data);
	}
	
	/**
	 * Encrypts the given string
	 * @param			string			$key
	 * @param			string			$data
	 * @param			string			$iv
	 * @throws			SystemException
	 * @return			string
	 */
	public static function encrypt($key, $data, $iv) {
		if (!static::encryptionAvailable()) throw new EncryptionException("Encryption is not supported by php installation");
		
		// open handle
		$handle = static::getEncryptionHandle();
		
		// init mcrypt
		if (mcrypt_generic_init($handle, $key, $iv) !== 0) throw new EncryptionException("Initialization of mcrypt failed");
		
		// encrypt
		return mcrypt_generic($handle, $data);
	}
	
	/**
	 * Encrypts given data for maintainer use (Nobody can decrypt this data without maintainer's secret key)
	 * @param			string				$data
	 * @throws			SystemException
	 * @return			string
	 */
	public static function encryptForMaintainer($data) {
		// check for openssl
		if (!extension_loaded('openssl')) throw new EncryptionException("Cannot encrypt data for maintainer: OpenSSL extension does not exist");
		
		// encrypt
		openssl_public_encrypt($data, $data, KeyManager::getMaintainerKey());
		
		return $data;
	}
	
	/**
	 * Returns true if encryption is supported by php installation
	 * @return			boolean
	 */
	public static function encryptionAvailable() {
		return (extension_loaded('mcrypt') and in_array(static::CRYPT_ALGORITHM, mcrypt_list_algorithms()));
	}
	
	/**
	 * Opens a mcrypt handle
	 * @throws			SystemException
	 * @return			resource
	 */
	protected static function getEncryptionHandle() {
		// open mcrypt handle
		$crypt = mcrypt_module_open(static::CRYPT_ALGORITHM, '', static::CRYPT_MODE, '');
		
		// validate handle
		if (!$crypt) throw new EncryptionFailedException("Cannot encrypt: An error occoured while opening mcrypt handle");
		
		return $crypt;
	}
	
	/**
	 * Hashes data with sha256
	 * @param			string			$data
	 * @return			string
	 */
	public static function hash($data) {
		return mhash(static::HASH_ALGORITHM, $data);
	}
}
?>