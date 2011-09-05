<?php
namespace ikarus\util;

/**
 * Manages encryptions
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
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
		if (!static::encryptionAvailable()) throw new SystemException("Encryption is not supported by php installation");
		
		// open handle
		$handle = static::getEncryptionHandle();
		
		// init mcrypt
		if (mcrypt_generic_init($handle, $key, $iv) !== 0) throw new SystemException("Initialization of mcrypt failed");
		
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
		if (!static::encryptionAvailable()) throw new SystemException("Encryption is not supported by php installation");
		
		// open handle
		$handle = static::getEncryptionHandle();
		
		// init mcrypt
		if (mcrypt_generic_init($handle, $key, $iv) !== 0) throw new SystemException("Initialization of mcrypt failed");
		
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
		if (!extension_loaded('openssl')) throw new SystemException("Cannot encrypt data for maintainer: OpenSSL extension does not exist");
		
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
		if (!$crypt) throw new SystemException("Cannot encrypt: An error occoured while opening mcrypt handle");
		
		return $crypt;
	}
}
?>