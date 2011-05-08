<?php
namespace ikarus\system\session;
use ikarus\util\UserUtil;

/**
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CookieSession extends SessionEditor {

	/**
	 * @see lib/data/DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		UserUtil::handleSessionArguments($this);
	}
}
?>