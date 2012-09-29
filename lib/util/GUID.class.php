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

use ikarus\system\Ikarus;

/**
 * Allows generation of GUIDs (In format XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX)
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class GUID {
	
	/**
	 * Defines a default prefix which is used to generate GUIDs.
	 * @var			string
	 */
	const DEFAULT_PREFIX = 'ikarus';
	
	/**
	 * Defines the GUID format (This should never change).
	 * @var			string
	 */
	const FORMAT = "XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX";
	
	/**
	 * Defines the bit which contains the prefix.
	 * @var			integer
	 */
	const PREFIX_BIT_INDEX = 3;
	
	/**
	 * Generates a new GUID.
	 * @param			string			$prefixInformation
	 * @return			string
	 */
	public function generate($prefixInformation = null) {
		// get default prefix if needed
		if ($prefixInformation === null) $prefixInformation = static::DEFAULT_PREFIX;
		
		// split parts to format
		$partTemplates = explode('-', static::FORMAT);
		
		$guid = "";
		
		// build
		foreach($partTemplates as $index => $template) {
			// add seperator
			if (!empty($guid)) $guid .= '-';
			
			// process "prefix"
			if ($index == static::PREFIX_BIT_INDEX) {
				$guid .= StringUtil::substring(HashManager::hash($prefixInformation), 0, strlen($template));
				continue;
			}
			
			// process
			$guid .= StringUtil::substring(HashManager::hash(Ikarus::getTime()), 0, strlen($template));
		}
		
		return $guid;
	}
}
?>