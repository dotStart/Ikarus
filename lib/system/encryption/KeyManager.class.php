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
namespace ikarus\system\encryption;

/**
 * Manages public and private keys.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class KeyManager {
	
	/**
	 * Constructs the object.
	 * @param			integer			$packageID
	 */
	public function __construct($packageID) {
		$this->packageID = $packageID;
		$this->loadCache();
	}
	
	/**
	 * Gets a key pair.
	 * @param			string			$alias
	 * @return			ikarus\system\encryption\KeyPair
	 * @throws			MissingKeyException
	 */
	public function getKey($alias) {
		if (!array_key_exists($alias, $this->keyList)) throw new MissingKeyException('Cannot find key "%s" in application pool "%s"', $alias, $this->packageID);
		return $this->keyList[$alias];
	}
	
	/**
	 * Loads all keys from cache.
	 * @return			void
	 */
	protected function loadCache() {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('keys-'.$this->packageID, 'keys-'.$this->packageID, 'ikarus\\system\\cache\\adapter\\CacheBuilderApplicationKeys', 0, 0, array('packageID' => $this->packageID));
		$this->keyList = Ikarus::getCacheManager()->getDefaultAdapter()->get('keys-'.$this->packageID);
	}
}
?>