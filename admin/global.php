<?php
/**
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */

// defines
define('RELATIVE_IKARUS_DIR', '../');

// include IKARUS
require_once(RELATIVE_IKARUS_DIR.'global.php');

// start admin panel
ikarus\system\IKARUS::init();
?>