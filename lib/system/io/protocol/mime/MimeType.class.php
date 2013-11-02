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
namespace ikarus\system\io\protocol\mime;

/**
 * Allows easy access to mime types.
 * @author                    Johannes Donath
 * @copyright                 © Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
class MimeType {

	/**
	 * Seperates the type from it's category in a string representation.
	 * @var                        string
	 */
	const TYPE_SEPERATOR = '/';

	/**
	 * Stores the type's category.
	 * @var                        string
	 */
	protected $category = '';

	/**
	 * Stores the type's exact name.
	 * @var                        string
	 */
	protected $type = '';

	/**
	 * Constructs the object.
	 * @param                        string $category
	 * @param                        string $type
	 */
	public function __construct ($category, $type) {
		$this->category = $category;
		$this->type = $type;
	}

	/**
	 * Constructs a mime type from a string representation.
	 * @param                        string $mimeString
	 * @return                        self
	 */
	public static function fromString ($mimeString) {
		$seperatorPosition = stripos ($mimeString, static::TYPE_SEPERATOR);

		// validate
		if ($seperatorPosition === false) throw new MimeTypeException('Cannot decode a malformed mime type string: Seperator %s is missing.', static::TYPE_SEPERATOR);

		return (new static(substr ($mimeString, 0, $seperatorPosition), substr ($mimeString, ($seperatorPosition + 1))));
	}

	/**
	 * Returns the mime type's category.
	 * @return                        string
	 */
	public function getCategory () {
		return $this->category;
	}

	/**
	 * Returns the type's name.
	 * @return                        string
	 */
	public function getType () {
		return $this->type;
	}

	/**
	 * Returns a string representation for this mime type.
	 * @return                        string
	 */
	public function __toString () {
		return $this->category . static::TYPE_SEPERATOR . $this->type;
	}
}

?>