<?php
namespace ikarus\system\cache\builder;

/**
 * Defines needed methods for cache builders
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface ICacheBuilder {
	
	/**
	 * Returns datat that should be written to cache
	 * @param			string			$resourceName
	 * @return			mixed
	 */
	public static function getData($resourceName);
}
?>