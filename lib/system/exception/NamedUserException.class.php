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
namespace ikarus\system\exception;

/**
 * The base class for all user specific problems.
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
abstract class NamedUserException extends SystemException {

	/**
	 * @see ikarus\system\exception.SystemException::EXCEPTION_TITLE
	 */
	const EXCEPTION_TITLE = 'Unknown problem';

	/**
	 * Contains a HTTP header that should be used
	 * @var                        string
	 */
	protected $header = 'HTTP/1.1 400 Bad Request';

	/**
	 * Creates a new instance of type SystemException
	 * @param                        string $message
	 */
	public function __construct ($message = '') {
		parent::__construct ($message);
	}

	/**
	 * @see ikarus\system\exception.SystemException::show()
	 */
	public function show () {
		parent::showMinimal ();
	}
}

?>