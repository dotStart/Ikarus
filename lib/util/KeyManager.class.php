<?php
namespace ikarus\util;
use ikarus\system\exception\KeyException;

/**
 * Manages all openssl keys used by ikarus
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class KeyManager {
	
	/**
	 * Returns the maintainer public key (Used to encrypt error reports)
	 * @throws			KeyException
	 * @return			string
	 */
	public static function getMaintainerKey() {
		// validate key file
		if (!file_exists(IKARUS_DIR.'keys/maintainer.pub')) throw new KeyException("Cannot load maintainer key: File does not exist!");
		
		// return content
		return file_get_contents(IKARUS_DIR.'keys/maintainer.pub');
	}
}
?>