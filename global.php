<?php
/**
 * Initialisizes the Ikarus Framework
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */

// defines
define('IKARUS_DIR', dirname(__FILE__).'/');
define('TIME_NOW', time());

// include framework core class
require_once(IKARUS_DIR.'lib/system/IKARUS.class.php');
?>