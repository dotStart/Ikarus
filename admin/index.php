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

require_once('./global.php');
ikarus\util\RequestHandler::handle(ikarus\util\ArrayUtil::appendSuffix($packageDirs, 'lib/acp/'));
?>