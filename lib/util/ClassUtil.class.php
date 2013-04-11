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
use ikarus\system\Ikarus;
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
	 * @throws			StrictStandardException
	 * @api
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
	 * Checks whether a class exists or not (includes interfaces)
	 * @param			string			$className
	 * @param			boolean			$autoload
	 * @return			boolean
	 * @api
	 */
	public static function classExists($className, $autoload = false) {
		return (class_exists($className, $autoload) or interface_exists($className, $autoload));
	}
	
	/**
	 * @see class_alias()
	 * @api
	 */
	public static function createAlias($originalClass, $aliasClass) {
		// convert object to string
		if (!is_string($originalClass)) $originalClass = get_class($originalClass);
		
		// dependency check
		if (!static::classExists($originalClass, true)) throw new MissingDependencyException("Cannot find class '%s'", $originalClass);
		
		// create alias
		return class_alias($originalClass, $aliasClass);
	}
	
	/**
	 * Returns a list of parent interfaces.
	 * @param			string			$className
	 * @return			array<string>
	 * @api
	 */
	public static function getInterfaces($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->getInterfaceNames();
	}
	
	/**
	 * @see ReflectionClass::getConstants()
	 * @api
	 */
	public static function getConstantList($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		$class = new ReflectionClass($className);
		return $class->getConstants();
	}
	
	/**
	 * Returns the namespace of the given class.
	 * @param			mixed			$className
	 * @return			string
	 * @api
	 */
	public static function getNamespace($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->getNamespaceName();
	}
	
	/**
	 * Returns a list of all parents.
	 * @param			mixed			$className
	 * @param			boolean			$getInterfaces			Set this to true if you need a list of interfaces, too.
	 * @return			array<string>
	 * @api
	 */
	public static function getParents($className, $getInterfaces = false) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// create default return value
		$parents = array();
		
		// get interfaces
		if ($getInterfaces) $parents = static::getInterfaces($className);
		
		// append parents
		$parent = (new ReflectionClass($className))->getParentClass();
		$lastParent = $parent->getName();
		while($parent->getName() != $lastParent) { // TODO: Verify this. Currently there's no documentation available for ReflectionClass::getParentClass()
			var_dump($parent->getName(), $parents);
			$parents[] = $parent->getName();
			$parent = $parent->getParentClass();
		}
		
		return $parents;
	}
	
	/**
	 * @see get_object_vars()
	 * @api
	 */
	public static function getPublicProperties($instance) {
		return get_object_vars($instance);
	}
	
	/**
	 * Checks whether a class is abstract.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 * @api
	 */
	public static function isAbstract($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->isAbstract();
	}
	
	/**
	 * Checks whether a class is instantiable.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 * @api
	 */
	public static function isInstantiable($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->isInstantiable();
	}
	
	/**
	 * Checks whether a class is an interface.
	 * @param			mixed			$className
	 * @return			boolean
	 * @throws			MissingDependencyException
	 * @api
	 */
	public static function isInterface($className) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		return interface_exists($className, true);
	}
	
	/**
	 * Returns true if the given class inherits from given target class
	 * @param			mixed			$className
	 * @param			mixed			$targetClass
	 * @api
	 */
	public static function isInstanceOf($className, $targetClass) {
		// convert objects to string
		if (!is_string($className)) $className = get_class($className);
		if (!is_string($targetClass)) $targetClass = get_class($targetClass);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		if (!static::classExists($targetClass, true)) throw new MissingDependencyException("Cannot find class '%s'", $targetClass);
		
		// equal check
		if ($className == $targetClass) return true;
		
		// normal classes
		if (static::classExists($targetClass)) return is_subclass_of($className, $targetClass);
		
		// interfaces
		if (interface_exists($targetClass)) {
			$reflectionClass = new ReflectionClass($className);
			return $reflectionClass->implementsInterface($targetClass);
		}
		
		// fallback
		return false;
	}
	
	/**
	 * Loads dependencies defined by a given class.
	 * @param			string			$className
	 * @return			void
	 * @api
	 */
	public static function loadDependencies($className) {
		$reflectionClass = new ReflectionClass($className);
		if (!$reflectionClass->hasConstant('DEPENDENCIES')) return;
		
		$constant = $reflectionClass->getConstant('DEPENDENCIES');
			
		$dependencies = explode(',', $constant);
		
		// check dependencies
		foreach($dependencies as $dependency) {
			if (!Ikarus::componentLoaded($dependency)) Ikarus::requestComponent($dependency);
		}
	}
	
	/**
	 * @see method_exists()
	 * @api
	 */
	public static function methodExists($className, $methodName) {
		// convert object to string
		if (!is_string($className)) $className = get_class($className);
		
		// dependency check
		if (!static::classExists($className, true)) throw new MissingDependencyException("Cannot find class '%s'", $className);
		
		// get information
		return method_exists($className, $methodName);
	}
}
?>