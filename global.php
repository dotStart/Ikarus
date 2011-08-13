<?php
/**
 * Initialisizes the Ikarus Framework
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */

// defines
define('IKARUS_DIR', dirname(__FILE__).'/');
define('TIME_NOW', time());

// include core functions and application core
require_once(IKARUS_DIR.'lib/system/IKARUS.class.php');
ikarus\system\IKARUS::init($packageList, $packageDir);
?>