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
namespace ikarus\util;

use ikarus\system\database\QueryEditor;
use ikarus\system\Ikarus;

/**
 * Provides methods for using dependency trees
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class DependencyUtil {

	/**
	 * Generates a basic dependency query.
	 * @param                        integer     $packageID
	 * @param                        QueryEditor $query
	 * @param                        string      $table
	 * @return                        void
	 * @api
	 */
	public static function generateDependencyQuery ($packageID, QueryEditor $query, $table) {
		$query->join (QueryEditor::LEFT_JOIN, array ('ikarus1_package_dependency' => 'dependency'), $table . '.packageID = dependency.packageID', '');
		$query->where ('dependency.packageID = ' . $packageID . ' OR dependency.dependencyID = ' . $packageID . ' OR ' . $table . '.packageID = ' . $packageID);
		$query->order ('dependency.dependencyLevel ASC');
	}

	/**
	 * Generates a basic instance query (A query which depends on an application instance).
	 * @param                        integer     $instanceID
	 * @param                        QueryEditor $query
	 * @param                        string      $table
	 * @return                        void
	 * @api
	 */
	public static function generateInstanceQuery ($instanceID, QueryEditor $query, $table) {
		if (Ikarus::getConfiguration ()->get ('application.dependency.inheritParentInstanceInformation')) {
			$query->join (QueryEditor::LEFT_JOIN, array ('ikarus1_package_instance' => 'instance'), $table . 'instanceID = instance.instanceID', '');
			static::generateDependencyQuery ('instance.packageID', $query, $table);
		}
		$query->where ($table . '.instanceID = ' . $instanceID);
	}
}

?>