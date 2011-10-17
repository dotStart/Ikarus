<?php
namespace ikarus\util;
use ikarus\system\database\QueryEditor;

/**
 * Provides methods for using dependency trees
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DependencyUtil {
	
	/**
	 * Generates a basic dependency query
	 * @param			integer				$packageID
	 * @param			QueryEditor			$query
	 * @param			string				$table
	 * @return			string
	 */
	public static function generateDependencyQuery($packageID, QueryEditor $query, $table) {
		$query->join(QueryEditor::LEFT_JOIN, array('ikarus'.IKARUS_N.'_package_dependency' => 'dependency'), $table.'.packageID = dependency.packageID', '');
		$query->where('dependency.packageID = '.$packageID);
		$query->where('dependency.dependencyID = '.$packageID, QueryEditor::TYPE_OR);
		$query->where($table.'.packageID = '.$packageID, QueryEditor::TYPE_OR);
		$query->order('dependency.dependencyLevel ASC');
	}
}
?>