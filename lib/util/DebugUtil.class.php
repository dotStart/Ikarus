<?php
namespace ikarus\util;

/**
 * Provides methods for application debugging
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DebugUtil {
	
	/**
	 * Prints a debug backtrace
	 * WARNING: Never use this method in productive systems. It can show internal information such as passwords
	 * @return			void
	 */
	public static function printBacktrace() {
		// get backtrace
		$trace = debug_backtrace();
		
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
		foreach(array_reverse($trace) as $index => $element) {
			$string .= "<tr>";
			$string .= "<td>".$index."</td>";
			$string .= "<td>".(isset($element['file']) ? $element['file'] : 'unknown')." (".(isset($element['line']) ? $element['line'] : 0).")</td>";
			$string .= "<td>".(isset($element['class']) ? $element['class'].$element['type'] : '').$element['function'].'(';

			foreach($element['args'] as $key => $argument) {
				if ($key > 0) $string .= ', ';
				$string .= gettype($argument);
				
				switch(gettype($argument)) {
					case 'array':
						$string .= '('.count($argument).')';
						break;
					case 'boolean':
						$string .= '('.($argument ? 'true' : 'false').')';
						break;
					case 'integer':
					case 'float':
					case 'double':
						$string .= '('.$argument.')';
						break;
					case 'object':
						if (function_exists('spl_object_hash')) $string .= '('.spl_object_hash($argument).')';
						break;
					case 'string':
						$string .= '('.strlen($argument).')';
						break;
				}
			}
			
			$string .= ")</td>";
			
			$string .= "<td><pre>".(isset($element['object']) ? StringUtil::encodeHTML(var_export($element['object'], true)) : '')."</pre></td></tr>";
		}
		
		// add footer
		$string .= "		</tbody>
				</table>";
		
		echo $string;
	}
}
?>