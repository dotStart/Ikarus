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
namespace ikarus\system\io;

use Countable;
use ikarus\system\exception\iterator\IteratorOutOfBoundsException;
use Iterator;

/**
 * Allows to iterate over directory contents.
 * @author                    Johannes Donath
 * @copyright                 2012 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class FilesystemDirectoryIterator implements Iterator, Countable, IFileInfo {

	/**
	 * Stores all directory contents.
	 * @var                        array<IFileInfo>
	 */
	protected $contents = array ();

	/**
	 * Stores the current iterator position.
	 * @var                        integer
	 */
	protected $pointer = 0;

	/**
	 * Constructs the object.
	 * @param                        array <IFileInfo>                        $contents
	 * @internal                        New instances should get created by the parent adapter.
	 */
	public function __construct (array $contents) {
		$this->contents = $contents;
	}

	/**
	 * @see Countable::count()
	 * @api
	 */
	public function count () {
		return count ($this->contents);
	}

	/**
	 * @see Iterator::current()
	 * @api
	 */
	public function current () {
		if (!array_key_exists ($this->key (), $this->contents)) throw new IteratorOutOfBoundsException();

		return $this->contents[$this->pointer];
	}

	/**
	 * @see ikarus\system\io.IFileInfo::isDirectory()
	 * @api
	 */
	public function isDirectory () {
		return false;
	}

	/**
	 * @see ikarus\system\io.IFileInfo::isFile()
	 * @api
	 */
	public function isFile () {
		return true;
	}

	/**
	 * @see Iterator::key()
	 * @api
	 */
	public function key () {
		$keys = array_keys ($this->contents);

		return $keys[$this->pointer];
	}

	/**
	 * @see Iterator::next()
	 * @api
	 */
	public function next () {
		$this->pointer++;
	}

	/**
	 * @see Iterator::rewind()
	 * @api
	 */
	public function rewind () {
		$this->pointer = 0;
	}

	/**
	 * @see Iterator::valid()
	 * @api
	 */
	public function valid () {
		return array_key_exists ($this->key (), $this->contents);
	}
}

?>