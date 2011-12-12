<?php
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;

/**
 * Caches the css definitions of a style
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderStyle implements ICacheBuilder {
	
	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName) {
		list($resourceName, $styleID) = explode('-', $resourceName);
		
		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_style_css' => 'style_css'));
		$editor->where('styleID = ?');
		$stmt = $editor->prepare();
		$stmt->bind($styleID);
		return $stmt->fetchList();
	}
}
?>