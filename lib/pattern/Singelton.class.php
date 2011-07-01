<?php
namespace ikarus\pattern;

/**
 * For lazy programmers: Singelton
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
abstract class Singelton {
	
	/**
	 * Contains an instance of type Singelton
	 * @var		Singelton
	 */
	protected static $instance = null;
	
	/**
	 * A protected construct method
	 */
	protected function __construct() { }
	
	/**
	 * A protected clone method
	 */
	protected function __clone() { }
	
	/**
	 * Returnes an instance of Singelton
	 * @return		Singelton
	 */
	public static function getInstance() {
		// create instance if needed
		if(static::$instance === null) static::$instance = new static();
		
		return static::$instance;
	}
}
?>