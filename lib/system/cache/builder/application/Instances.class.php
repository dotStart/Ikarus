<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\system\cache\builder\application;

use ikarus\data\application\Instance;
use ikarus\system\cache\builder\ICacheBuilder;
use ikarus\system\database\QueryEditor;

/**
 * Caches a list of all available application instances (ignoring dependencies).
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class Instances implements ICacheBuilder {

	/**
	 * @see ikarus\system\cache.CacheBuilder::getData()
	 */
	public static function getData ($resourceName, array $additionalParameters) {
		$editor = new QueryEditor();
		$editor->from (array('ikarus1_instance' => 'app_instance'));
		$editor->join (QueryEditor::LEFT_JOIN, array('ikarus1_application' => 'app'), 'app_instance.applicationID = app.applicationID', 'app.packageID');
		$stmt = $editor->prepare ();

		$instanceList = array();

		foreach ($stmt->fetchList () as $item) {
			$instanceList[] = new Instance(null, $item);
		}

		return $instanceList;
	}
}

?>