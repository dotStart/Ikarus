<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\util;

/**
 * Provides methods for application debugging
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class DebugUtil {

	/**
	 * Prints a debug backtrace
	 * WARNING: Never use this method in productive systems. It can show internal information such as passwords
	 * @return                        void
	 * @api
	 */
	public static function printBacktrace () {
		// get backtrace
		$trace = debug_backtrace ();

		// add header
		$string = "<table border=\"1\">
				<thead>
					<tr>
						<th>Call number</th>
						<th>File</th>
						<th>Call</th>
						<th>Information</th>
					</tr>
				</thead>
				<tbody>";

		// add elements
		foreach (array_reverse ($trace) as $index => $element) {
			$string .= "<tr>";
			$string .= "<td>" . $index . "</td>";
			$string .= "<td>" . (isset($element['file']) ? $element['file'] : 'unknown') . " (" . (isset($element['line']) ? $element['line'] : 0) . ")</td>";
			$string .= "<td>" . (isset($element['class']) ? $element['class'] . $element['type'] : '') . $element['function'] . '(';

			foreach ($element['args'] as $key => $argument) {
				if ($key > 0) $string .= ', ';
				$string .= gettype ($argument);

				switch (gettype ($argument)) {
					case 'array':
						$string .= '(' . count ($argument) . ')';
						break;
					case 'boolean':
						$string .= '(' . ($argument ? 'true' : 'false') . ')';
						break;
					case 'integer':
					case 'float':
					case 'double':
						$string .= '(' . $argument . ')';
						break;
					case 'object':
						if (function_exists ('spl_object_hash')) $string .= '(' . spl_object_hash ($argument) . ')';
						break;
					case 'string':
						$string .= '(' . strlen ($argument) . ')';
						break;
				}
			}

			$string .= ")</td>";

			$string .= "<td><pre>" . (isset($element['object']) ? StringUtil::encodeHTML (var_export ($element['object'], true)) : '') . "</pre></td></tr>";
		}

		// add footer
		$string .= "		</tbody>
				</table>";

		echo $string;
	}
}

?>