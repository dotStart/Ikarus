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
	 * @see class_alias()
	 */
	public static function createAlias($originalClass, $aliasClass) {
		return class_alias($originalClass, $aliasClass);
	}
	
	/**
	 * @see ReflectionClass::getConstants()
	 */
	public static function getConstantList($className) {
		$class = new ReflectionClass($className);
		return $class->getConstants();
	}
	
	/**
	 * Returns the namespace of the given class.
	 * @param			mixed			$className
	 * @return			string
	 */
	public static function getNamespace($className) {
		if (!is_string($className)) $className = get_class($className);
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->getNamespaceName();
	}
	
	/**
	 * @see get_object_vars()
	 */
	public static function getPublicProperties($class) {
		return get_object_vars($class);
	}
	
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
	
	/**
	 * @see method_exists()
	 */
	public static function methodExists($targetClass, $methodName) {
		return method_exists($targetClass, $methodName);
	}
}
?>