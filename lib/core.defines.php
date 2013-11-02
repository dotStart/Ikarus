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
namespace ikarus;

	/**
	 * @author                    Johannes Donath
	 * @copyright                 2011 Evil-Co.de
	 * @package                   de.ikarus-framework.core
	 * @subpackage                system
	 * @category                  Ikarus Framework
	 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
	 * @version                   2.0.0-0001
	 */

	/** version defines **/
/**
 * Contains the complete Ikarus version (Major.Minor.Revision).
 * @var                        string
 */
define('IKARUS_VERSION', '2.0.0');

/**
 * Contains Ikarus' major version.
 * @var                        integer
 */
define('IKARUS_VERSION_MAJOR', 2);

/**
 * Contains Ikarus' minor version.
 * @var                        integer
 */
define('IKARUS_VERSION_MINOR', 0);

/**
 * Contains Ikarus' revision.
 * @var                        integer
 */
define('IKARUS_VERSION_REVISION', 0);

/**
 * Contains Ikarus' build ID.
 * Note: This is currently unused.
 * @var                        integer
 */
define('IKARUS_VERSION_BUILD', '0001');

/** package defines **/
/**
 * Defines Ikarus' package ID.
 * @var                        integer
 * @deprecated                This is not used. The database adapter replaces the packageIDs automatically.
 */
define('IKARUS_ID', 1);
?>