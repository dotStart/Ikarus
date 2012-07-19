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
use ikarus\system\exception\MissingDependencyException;
use ikarus\system\exception\StrictStandardException;
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
	 * Builds a new class path.
	 * @param			string			$part1
	 * @param			string			$part2
	 * @param			string			...
	 * @throws StrictStandardException
	 */
	public static function buildPath() {
		// Dumb developer
		if (count(func_get_args()) <= 0) throw new StrictStandardException("You can't build an empty class path.");
		
		// build path
		$path = "";
		
		foreach(func_get_args() as $element) {
			// kill \ on index 0 (if any)
			if ($element{0} == '\\') $element = substr($element, 1);
			
			// append part
			$path .= $element;
			
			// add a new \ at the end
			if (substr($element, -1) != '\\') $path .= '\\';
		}
		
		// remove last \
		return substr($path, 0, (strlen($path) - 1));
	}
	
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
	 * Checks whether a class is abstract.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 */
	public static function isAbstract($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!class_exists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->isAbstract();
	}
	
	/**
	 * Checks whether a class is instantiable.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 */
	public static function isInstantiable($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!class_exists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->isInstantiable();
	}
	
	/**
	 * Checks whether a class is an interface.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 */
	public static function isInterface($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!class_exists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		return interface_exists($className, true);
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