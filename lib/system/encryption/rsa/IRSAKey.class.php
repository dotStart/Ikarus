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
namespace ikarus\system\encryption\rsa;

/**
 * Defines needed methods for RSA keys.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface IRSAKey {

	/**
	 * Defines an unknown key.
	 * @var			integer
	 */
	const TYPE_UNKNOWN = -1;

	/**
	 * Defines a private key.
	 * @var			integer
	 */
	const TYPE_PRIVATE = 0;

	/**
	 * Defines a public key.
	 * @var			integer
	 */
	const TYPE_PUBLIC = 1;

	/**
	 * Decodes an encoded key.
	 * @param			string			$data
	 * @return			self
	 */
	public static function decode($data);

	/**
	 * Returns a binary version of this key.
	 * @return			string
	 */
	public function getBinaryVersion();

	/**
	 * Returns an encoded version of this key.
	 * @return			string
	 */
	public function getEncodedVersion();

	/**
	 * Returns the type of this key.
	 * @return			integer
	 */
	public function getType();
}
?>