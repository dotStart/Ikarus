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
namespace ikarus\system\io;
use ikarus\system\exception\MissingDependencyException;
use ikarus\system\Ikarus;
use ikarus\util\StringUtil;

/**
 * Manages web outputs (Such as XML, HTML or JSON).
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class WebOutputManager {
	
	/**
	 * Generates a new output handle.
	 * @param			mixed			$data
	 * @param			string			$outputType			xml, json, html, debug and custom output handlers are supported.
	 * @return			OutputHandle
	 */
	public function generateOutput($data, $outputType = null) {
		// get default output type
		if ($outputType === null) $outputType = Ikarus::getConfiguration()->get('output.general.type');
		
		// construct correct handle.
		switch($outputType) {
			case 'xml':
				return new XMLOutputHandle($data);
				break;
			case 'json':
				return new JSONOutputHandle($data);
				break;
			case 'html':
				return new HTMLOutputHandle($data);
				break;
			case 'debug':
				return new DebugOutputHandle($data);
				break;
			default:
				// build className
				$className = StringUtil::firstCharToUpperCase($outputType).'OutputHandle';
				
				// validate class
				if (!class_exists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
				
				// construct
				return new $className($data);
				break;
		}
	}
}
?>