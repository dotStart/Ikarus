<?php
/**
 * This file is part of the Ikarus Framework.
 *
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\cache\builder;
use ikarus\system\database\QueryEditor;
use ikarus\system\style\Style;
use ikarus\util\DependencyUtil;

/**
 * Caches all controller types
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class CacheBuilderStyleList implements ICacheBuilder {

	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData($resourceName, $additionalCacheBuilderParameters = array()) {
		list($resourceName, $packageID, $environment) = explode('-', $resourceName);

		$editor = new QueryEditor();
		$editor->from(array('ikarus'.IKARUS_N.'_style' => 'style'));
		$editor->where('environment = ?');
		$editor->where('isEnabled = 1');
		DependencyUtil::generateDependencyQuery($packageID, $editor, 'style');
		$stmt = $editor->prepare();
		$stmt->bind($environment);
		$resultList = $stmt->fetchList();

		$styleList = array();

		foreach($resultList as $result) {
			$styleList[] = new Style($result->__toArray());
		}

		return $styleList;
	}
}
?>