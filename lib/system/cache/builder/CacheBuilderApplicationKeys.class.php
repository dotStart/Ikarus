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
namespace ikarus\system\cache\builder;
use ikarus\data\encryption\KeyPair;
use ikarus\data\encryption\Key;
use ikarus\system\database\QueryEditor;
use ikarus\util\DependencyUtil;

/**
 * Caches application keys (to sign or verify).
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderApplicationKeys implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache\builder\ICacheBuilder
	 */
	public static function getData($resourceName, $additionalParameters) {
		$editor = QueryEditor();
		$editor->from(array('ikarus1_encryption_key' => 'encryption_key'));
		DependencyUtil::generateDependencyQuery($additionalParameters['packageID'], $editor, 'encryption_key');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();
		
		$keyList = array();
		
		foreach($resultList as $key) {
			$keyList[$key->keyID] = new Key(null, $key);
		}
		
		$editor = QueryEditor();
		$editor->from(array('ikarus1_encryption_key_pair' => 'encryption_key_pair'));
		DependencyUtil::generateDependencyQuery($additionalParameters['packageID'], $editor, 'encryption_key');
		$stmt = $editor->prepare();
		$resultList = $stmt->fetchList();
		
		$keyPairList = array();
		
		foreach($resultList as $result) {
			$keyPairList[] = new KeyPair(($result->publicKey ? $keyList[$result->publicKey] : null), ($result->privateKey ? $keyList[$result->privateKey] : null));
		}
		
		return $keyPairList;
	}
}
?>