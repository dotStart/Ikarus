<?php
namespace ikarus\util;
use \ReflectionClass;

/**
 * Provides methods for analysing classes
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ClassUtil {
	
	/**
	 * Returns true if the given class inherits from given target class
	 * @param			mixed			$className
	 * @param			mixed			$targetClass
	 */
	public static function isInstanceOf($className, $targetClass) {
		// convert objects to string
		if (!is_string($className)) $className = get_class($className);
		if (!is_string($targetClass)) $targetClass = get_class($targetClass);
		
		// normal classes
		if (class_exists($targetClass)) return is_subclass_of($className, $targetClass);
		
		// interfaces
		if (interface_exists($targetClass)) {
			$reflectionClass = new ReflectionClass($className);
			return $reflectionClass->implementsInterface($targetClass);
		}
		
		return false;
	}
}
?>