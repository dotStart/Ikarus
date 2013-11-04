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
namespace ikarus\system\test;

use ikarus\pattern\Singleton;

/**
 * This class provides methods for running unit tests
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   1.0.0-0001
 */
class UnitTestHelper extends Singleton {

	/**
	 * Contains a count of succeeded tests
	 * @var                        integer
	 */
	protected $succeededTests = 0;

	/**
	 * Contains a count of failed tests
	 * @var                        integer
	 */
	protected $failedTests = 0;

	/**
	 * Contains a count of executed tests
	 * @var                        integer
	 */
	protected $executedTests = 0;

	/**
	 * Executes an assertion for given variable
	 * @param                string $message This message will appear if the test fails
	 * @param                mixed  $variable
	 * @throws                SystemException
	 * @return                void
	 */
	public function assert ($message, $variable) {
		// count tests
		$this->executedTests++;

		// count succeeded tests
		if ($variable) {
			$this->succeededTests++;

			return;
		}

		// increment failed test count
		$this->failedTests++;

		// display error message
		try {
			throw new SystemException("Unit test failed: %s", $message);
		} catch (SystemException $ex) {
			$ex->show (); // Just a little workaround to get the default SystemException design without killing the whole process
		}
	}

	/**
	 * Checks for equal variables
	 * @param                string $message
	 * @param                mixed  $variable1
	 * @param                mixed  $variable2
	 * @return                void
	 * @api
	 */
	public function assertEqual ($message, $variable1, $variable2) {
		$this->assert ($message, $variable1 == $variable2);
	}

	/**
	 * Checks for false variables
	 * @param                string $message
	 * @param                mixed  $variable
	 * @return                void
	 * @api
	 */
	public function assertFalse ($message, $variable) {
		$this->assert ($message, !(bool)$variable);
	}

	/**
	 * Checks for identical variables
	 * @param                string $message
	 * @param                mixed  $variable1
	 * @param                mixed  $variable2
	 * @return                void
	 * @api
	 */
	public function assertIdentical ($message, $variable1, $variable2) {
		$this->assert ($message, $variable1 === $variable2);
	}

	/**
	 * Checks for true variables
	 * @param                string $message
	 * @param                mixed  $variable
	 * @return                void
	 * @api
	 */
	public function assertTrue ($message, $variable) {
		$this->assert ($message, (bool)$variable);
	}

	/**
	 * Executes all tests of given class
	 * @param                object $testClass
	 * @return                void
	 * @api
	 */
	public function executeTests ($testClass) {
		// get reflection instance
		$reflection = new ReflectionClass($testClass);

		// loop through methods
		foreach ($reflection->getMethods () as $method) {
			try {
				// run test
				if (substr ($method, 0, 4) == 'test') $method->invoke (static::$instance);
			} catch (SystemException $ex) {
				// let it run ...
				$ex->show ();
			}
		}
	}
}

?>