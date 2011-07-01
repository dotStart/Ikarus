<?php
namespace ikarus\system\test;

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
class UnitTestHelper {
	
	/**
	 * Contains an instance of type UnitTestHelper
	 * @var			UnitTestHelper
	 */
	protected static $instance = null;
	
	/**
	 * Creates a new instance of type UnitTestHelper
	 */
	protected function __construct() { }
	
	/**
	 * Clones an instance of type UnitTestHelper
	 * @return		UnitTestHelper
	 */
	protected function __clone() { }
	
	/**
	 * Executes all tests of given class
	 * @param		object		$testClass
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
	
	/**
	 * Returnes an instance of type UnitTestHelper
	 * @return		UnitTestHelper
	 */
	public static function getInstance() {
		// create new instance if needed
		if (static::$instance === null) static::$instance = new static();
		return static::$instance;
	}
}
?>