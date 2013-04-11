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
namespace ikarus\system\auth;

use ikarus\system\exception\api\APIException;
use ikarus\util\ClassUtil;

/**
 * Allows user objects to be expandable.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class ExtendableUserObject extends DatabaseObject {
	
	/**
	 * Contains a list of registered user object extensions.
	 * @var			IUserObjectExtension[]
	 */
	protected $extensions = array();
	
	/**
	 * Registers a new 
	 * @param			string					$extension
	 * @return			void
	 */
	public function registerExtension($extensionClass) {
		// create new extension instance
		$extension = new $extensionClass($this);
		
		// check for already registered instances
		if (in_array($extension, $this->extensions)) return;
		
		// register
		$this->extensions[] = $extension;
		
		// dubdiduh
		return $extension;
	}
	
	/**
	 * Unregisters an extension.
	 * @param			IUserObjectExtension			$extension
	 * @return			void
	 */
	public function unregisterExtension(IUserObjectExtension $extension) {
		foreach($this->extensions as $key => $registeredExtension) {
			if ($registeredExtension == $extension) unset($this->extensions[$key]);
		}
	}
	
	/**
	 * Forwards update() calls to all extensions.
	 * @return			void
	 */
	public function update() {
		foreach($this->extensions as $extension) {
			$extension->update();
		}
	}
	
	/**
	 * Emulates extension calls.
	 * @param			string			$methodName
	 * @param			mixed[]			$arguments
	 * @return			mixed
	 * @throws			APIException
	 */
	public function __call($methodName, $arguments) {
		foreach($this->extensions as $extension) {
			if (method_exists($extension, $arguments)) return $extension->$methodName($arguments);
		}
		
		return parent::__call($methodName, $arguments);
	}
	
	/**
	 * Emulates property get-requests.
	 * @param			string			$propertyName
	 * @return			mixed
	 * @throws			APIException
	 */
	public function __get($propertyName) {
		foreach($this->extensions as $extension) {
			if (isset($extension->{$propertyName})) return $extension->{$propertyName};
		}
		
		return parent::__get($propertyName);
	}
	
	/**
	 * Emulates isset() calls on propreties.
	 * @param			string			$propertyName
	 * @return			boolean
	 */
	public function __isset($propertyName) {
		foreach($this->extensions as $extension) {
			if (isset($extension->{$propertyName})) return true;
		}
		
		return parent::__isset($propertyName);
	}
}
?>