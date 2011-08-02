<?php
/**
 * Initialisizes the Ikarus Framework
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */

// defines
define('IKARUS_DIR', dirname(__FILE__).'/');
define('TIME_NOW', time());

// define packages
if (!isset($packageList)) $packageList = array();
$packageList['ikarus'] = IKARUS_DIR;

// define current package dir
if (!isset($packageDir)) $packageDir = IKARUS_DIR;

// include core functions and application core
require_once(IKARUS_DIR.'lib/system/IKARUS.class.php');
require_once(IKARUS_DIR.'lib/core.functions.php');
ikarus\system\IKARUS::init($packageList, $packageDir);
?>