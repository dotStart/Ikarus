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
use ikarus\system\exception\StrictStandardException;
use ikarus\system\Ikarus;

/**
 * Allows to generate V5 UUIDs.
 * Note: This works with pure PHP 5.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class UUID {
	
	/**
	 * Stores the regex for a valid UUID.
	 * @var			string
	 */
	const UUID_REGEX = '~^\{?([0-9a-f]{8})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{12})\}?$~i';
	
	/**
	 * Creates a new UUID.
	 * @param			string			$namespace			A namespace UUID.
	 * @param			string			$resourceName	
	 * @return			string		
	 */
	public static function create($namespace, $resourceName) {
		// validate namespace
		if (!static::isUUID($namespace)) throw new StrictStandardException('Cannot use a random value as UUID namespace.');
		
		// remove non-hex characters from namespace
		$hexNamespace = str_replace(array(
			'{', '}',
			'-'
		), '', $namespace);
		
		// init binary version variable
		$binaryNamespace = '';
		
		// iterate over each number
		for($i = 0; $i < strlen($hexNamespace); $i += 2) {
			// get character
			$binaryNamespace .= chr(hexdec($hexNamespace{$i}.$hexNamespace{$i + 1}));
		}
		
		// calculate hash
		$hash = sha1($binaryNamespace.$resourceName);
		
		// build UUID
		return sprintf('%08s-%04s-%04x-%04x-%12s',
			// 32 bits (time_low)
			substr($hash, 0, 8),
			
			// 16 bits (time_mid)
			substr($hash, 8, 4),
			
			// 16 bits (time_hi_and_version)
			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
			
			// 16 bits, 8 bits (clk_seq_hi_res)
			// 8 bits (clk_seq_low)
			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
				
			// 48 bits (node)
			substr($hash, 20, 12)
		);
	}
	
	/**
	 * Generates a pseudo random v4 UUID which can be used for root namespaces.
	 * @return			string
	 * @api
	 */
	public static function generateNamespace() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits (time_low")
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		
			// 16 bits (time_mid)
			mt_rand(0, 0xffff),
		
			// 16 bits (time_hi_and_version)
			mt_rand(0, 0x0fff) | 0x4000,
		
			// 16 bits, 8 bits (clk_seq_hi_res)
			// 8 bits for "clk_seq_low",
			mt_rand(0, 0x3fff) | 0x8000,
		
			// 48 bits (node)
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
	
	/**
	 * Returns the root namespace UUID.
	 * @return			string
	 * @api
	 */
	public static function getRootNamespace() {
		// regenerate root namespace
		static::initRootNamespace();
		
		// return root UUID
		return Ikarus::getConfiguration()->get('system.internal.rootUUID');
	}
	
	/**
	 * Inits the root namespace.
	 * @return			void
	 * @api
	 */
	public static function initRootNamespace() {
		// try to get root namespace from configuration
		if (Ikarus::getConfiguration()->get('system.internal.rootUUID') === null) {
			// generate new one
			$rootUUID = static::generateNamespace();
			
			// store in database
			$sql = "UPDATE
					ikarus1_option
				SET
					optionValue = ?
				WHERE
					optionName = ?
				LIMIT 1";
			$stmt = Ikarus::getDatabaseManager()->getDefaultAdapter()->prepareStatement($sql);
			$stmt->bind($rootUUID);
			$stmt->bind('system.internal.rootUUID');
			$stmt->execute();
			
			// rewrite option file
			Ikarus::getConfiguration()->regenerate();
			
			// set during runtime
			Ikarus::getConfiguration()->set('system.internal.rootUUID', $rootUUID);
		}
	}
	
	/**
	 * Checks whether the specified UUID is valid or not.
	 * @param			string			$uuid
	 * @return			boolean
	 * @api
	 */
	public static function isUUID($uuid) {
		return preg_match(static::UUID_REGEX, $uuid) === 1;
	}
}
?>