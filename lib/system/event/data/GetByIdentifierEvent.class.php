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
namespace ikarus\system\event\data;

/**
 * This event occurs if the DatabaseObject::GetByIdentifier() method is called.
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class GetByIdentifierEvent extends CancellableDataEvent {

	/**
	 * Contains a replacement for returned database object.
	 * @var                        DatabaseObject
	 */
	protected $replacement = null;

	/**
	 * Gets the replacement.
	 * @return \ikarus\system\event\data\DatabaseObject
	 */
	public function getReplacement () {
		return $this->replacement;
	}

	/**
	 * Sets a replacement.
	 * @param                        DatabaseObject $object
	 */
	public function setReplacement (DatabaseObject $object) {
		$this->replacement = $object;
	}
}

?>