<?php
namespace ikarus\system\session;

/**
 * Defines needed methods for sessions
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface ISession {
	
	/**
	 * Creates a new instance of type ISession
	 * @param			array			$data
	 */
	public function __construct($data);
	
	/**
	 * Updates the database row of this session
	 * @return			void
	 */
	public function update();
}
?>