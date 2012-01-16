<?php
namespace ikarus\pattern;
use ikarus\system\exception\StrictStandardException;

/**
 * For lazy programmers: Singleton
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class Singleton {
	
	/**
	 * Contains an instance of type Singelton
	 * @var			array<Singelton>
	 */
	protected static $instances = array();
	
	/**
	 * A protected construct method
	 */
	protected function __construct() {
		$this->init();
	}
	
	/**
	 * Replaces the __construct() method
	 * Note: You have to use this method to init own components
	 * @return			void
	 */
	public function init() { }
	
	/**
	 * A protected clone method
	 */
	protected function __clone() { }
	
	/**
	 * Disallows serialize()
	 * @throws			StrictStandardException
	 * @return			void
	 */
	public function __sleep() {
		throw new StrictStandardException("It's not allowed to serialize singletons");
	}
	
	/**
	 * Returnes an instance of Singelton
	 * @return		Singelton
	 */
	public static function getInstance() {
		// get class
		$className = get_called_class();
		
		// create instance if needed
		if(array_key_exists(static::$instances[$className])) static::$instances[$className] = new $className();
		
		// return instance
		return static::$instances[$class];
	}
}
?>