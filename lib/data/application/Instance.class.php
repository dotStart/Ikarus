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
namespace ikarus\data\application;
use ikarus\data\DatabaseObject;
use ikarus\system\database\QueryEditor;

/**
 * Represents an instance row.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class Instance extends DatabaseObject {
	
	/**
	 * Constructs the object.
	 * @param			integer			$instanceID
	 * @param			mixed			$row
	 */
	public function __construct($instanceID, $row = null) {
		if ($instanceID != null) {
			$editor = new QueryEditor();
			$editor->from(array('ikarus1_instance' => 'app_instance'));
			$editor->where('instanceID = ?');
			$editor->limit(1);
			$stmt = $editor->prepare();
			$stmt->bind($instanceID);
			
			$row = $stmt->fetch();
		}
		
		parent::__construct($row);
	}
}
?>