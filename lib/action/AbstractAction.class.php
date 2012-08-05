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
namespace ikarus\action;
use ikarus\system\exception\MissingDependencyException;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\util\ClassUtil;

/**
 * The base class for all actions.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class AbstractAction implements IAction {

	/**
	 * Contains an error message produced by this resource.
	 * @var	string
	 */
	public $errorMessage = 'Success';

	/**
	 * Contains an error number produced by this resource.
	 * @var	integer
	 */
	public $errorNumber = 0;

	/**
	 * Contains an array or a string containing the permissions needed to access this action.
	 * @var	mixed
	 */
	public $neededPermissions = '';

	/**
	 * Contains the data that should be encoded as json.
	 * @var	mixed
	 */
	public $returnData = null;

	/**
	 * Defines dependencies for this resource.
	 * @var	array
	 */
	public $requirements = array();

	/**
	 * @see ikarus\action.IAction::__construct()
	 */
	public function __construct() {
		// fire construct@AbstractAction
		Ikarus::getEventManager()->fire($this, 'construct', 'actionMethod');
	}

	/**
	 * @see ikarus\action.IAction::init()
	 */
	public final function init() {
		// fire init@AbstractAction
		Ikarus::getEventManager()->fire($this, 'init', 'actionMethod');

		// add default dependencies
		$this->registerDependencies();

		// check dependencies
		try {
			// check
			$this->checkDependencies();

			// fire dependencyCheckSucceeded@AbstractAction
			Ikarus::getEventManager()->fire($this, 'dependencyCheckSucceeded', 'dependencyCheckStatusChanged');
		} catch (MissingDependencyException $ex) {
			// fire dependencyCheckFailed@AbstractAction
			Ikarus::getEventManager()->fire($this, 'dependencyCheckFailed', 'dependencyCheckStatusChanged');

			// throw exception
			throw $ex;
		}

		// read data
		$this->readParameters();
		$this->readData();

		// check permissions
		try {
			// check
			$this->checkPermissions();

			// fire permissionCheckSucceeded@AbstractAction and parent permissionCheckStatusChanged@AbstractAction
			Ikarus::getEventManager()->fire($this, 'permissionCheckSucceeded', 'permissionCheckStatusChanged');
		} catch (SystemException $ex) {
			// fire permissionCheckFailed@AbstractAction and parent permissionCheckStatusChanged@AbstractAction
			Ikarus::getEventManager()->fire($this, 'permissionCheckFailed', 'permissionCheckStatusChanged');

			// throw exception
			throw $ex;
		}

		// show data
		$this->show();

		// fire initFinished@AbstractAction
		Ikarus::getEventManager()->fire($this, 'initFinished');
	}

	/**
	 * Checks against all defined class dependencies.
	 * @throws MissingDependencyException
	 */
	public function checkDependencies() {
		// fire checkDependencies@AbstractAction
		Ikarus::getEventManager()->fire($this, 'checkDependencies', 'actionMethod');

		// check each dependency
		foreach($this->requirements as $abbreviation => $dependency) {
			if ((is_integer($abbreviation) and !Ikarus::componentLoaded($dependency)) or (is_string($abbreviation)) and !Ikraus::componentLoaded($dependency, $abbreviation)) throw new MissingDependencyException("Cannot find dependency '%s'", $dependency);
		}
	}

	/**
	 * Checks for needed permissions.
	 * @return			void
	 * @throws			SystemException
	 */
	public function checkPermissions() {
		// fire checkPermissions@AbstractAction
		Ikarus::getEventManager()->fire($this, 'checkPermissions', 'actionMethod');

		// check needed permissions
		if (!empty($this->neededPermissions)) Ikarus::getGroupManager()->getGroupHandle(Ikarus::getUser())->checkPermission($this->neededPermissions);

		// fire defaultPermissionsChecked@AbstractAction
		Ikarus::getEventManager()->fire($this, 'defaultPermissionsChecked');
	}

	/**
	 * Reads information.
	 * @return			void
	 */
	public function readData() {
		// fire readData@AbstractAction
		Ikarus::getEventManager()->fire($this, 'readData', 'actionMethod');
	}

	/**
	 * Reads all given parameters.
	 * @return			void
	 */
	public function readParameters() {
		// fire readParameters@AbstractAction
		Ikarus::getEventManager()->fire($this, 'readParameters', 'actionMethod');
	}

	/**
	 * Registers dependencies.
	 * Note: This is only needed for abstract classes.
	 */
	public function registerDependencies() {
		// fire registerDependencies@AbstractAction
		Ikarus::getEventManager()->fire($this, 'registerDependencies', 'actionMethod');

		// group manager
		$this->requirements['GroupManager'] = 'ikarus\\system\\group\\GroupManager';
		$this->requirements['WebOutputManager'] = 'ikarus\\system\\io\\WebOutputManager';
	}

	/**
	 * Shows the data generated by this resource.
	 * @return			void
	 */
	public function show() {
		// fire show@AbstractAction
		Ikarus::getEventManager()->fire($this, 'show', 'actionMethod');

		// get data
		$data = $this->returnData;

		// object support
		if (is_object($data)) {
			// serialize support
			if (ClassUtil::isInstanceOf($data, '\\Serializable'))
				$data = serialize($data);

			// __toString() support (fallback)
			else
				$data = (string) $data;
		}

		// fix information (encoding related problems)
		if (is_array($data)) {
			foreach($data as $key => $val) {
				if (is_string($val)) $data[$key] = utf8_encode($val);
			}
		} elseif (is_string($data))
			$data = utf8_encode($data);

		// encode data
		$returnValue = array(
			'errorNumber'		=>	$this->errorNumber,
			'errorMessage'		=>	$this->errorMessage,
		);
		if (!is_null($data) and !empty($data)) $returnValue['data'] = $data;

		// generate output
		$output = Ikarus::getWebOutputManager()->generateOutput($returnValue);

		// show output
		$output->render();
	}
}
?>