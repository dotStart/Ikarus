<?php
namespace ikarus\system\test;
use ikarus\pattern\Singleton;

/**
 * This class provides methods for running unit tests
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class UnitTestHelper extends Singleton {
	
	/**
	 * Contains a count of succeeded tests
	 * @var			integer
	 */
	protected $succeededTests = 0;
	
	/**
	 * Contains a count of failed tests
	 * @var			integer
	 */
	protected $failedTests = 0;
	
	/**
	 * Contains a count of executed tests
	 * @var			integer
	 */
	protected $executedTests = 0;
	
	/**
	 * Executes an assertion for given variable
	 * @param		string			$message			This message will appear if the test fails
	 * @param		mixed			$variable
	 * @throws		SystemException
	 * @return		void
	 */
	public function assert($message, $variable) {
		// count tests
		$this->executedTests++;
		
		// count succeeded tests
		if ($variable) {
			$this->succeededTests++;
			return;
		}
		
		$this->failedTests++;
		try {
			throw new SystemException("Unit test failed: %s", $message);
		} catch (SystemException $ex) {
			$ex->show(); // Just a little workaround to get the default SystemException design without killing the whole process
		}
	}
	
	/**
	 * Checks for equal variables
	 * @param		string			$message
	 * @param		mixed			$variable1
	 * @param		mixed			$variable2
	 * @return		void
	 */
	public function assertEqual($message, $variable1, $variable2) {
		$this->assert($message, $variable1 == $variable2);
	}
	
	/**
	 * Checks for false variables
	 * @param		string			$message
	 * @param		mixed			$variable
	 * @return		void
	 */
	public function assertFalse($message, $variable) {
		$this->assert($message, !(bool) $variable);
	}
	
	/**
	 * Checks for identical variables
	 * @param		string			$message
	 * @param		mixed			$variable1
	 * @param		mixed			$variable2
	 * @return		void
	 */
	public function assertIdentical($message, $variable1, $variable2) {
		$this->assert($message, $variable1 === $variable2);
	}
	
	/**
	 * Checks for true variables
	 * @param		string			$message
	 * @param		mixed			$variable
	 * @return		void
	 */
	public function assertTrue($message, $variable) {
		$this->assert($message, (bool) $variable);
	}
	
	/**
	 * Executes all tests of given class
	 * @param		object		$testClass
	 * @return		void
	 */
	public function executeTests($testClass) {
		// get reflection instance
		$reflection = new ReflectionClass($testClass);
		
		// loop through methods
		foreach($reflection->getMethods() as $method) {
			try {
				// run test
				if (substr($method, 0, 4) == 'test') $method->invoke(static::$instance);
			} catch (SystemException $ex) {
				// let it run ...
				$ex->show();
			}
		}
	}
}
?>