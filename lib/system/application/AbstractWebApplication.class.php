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
namespace ikarus\system\application;

use ikarus\system\Ikarus;
use ikarus\system\request\RequestDispatcher;

/**
 * Implements an application that loads components that are often used in web applications
 * @author                    Johannes Donath
 * @copyright                 2011 Evil-Co.de
 * @package                   de.ikarus-framework.core
 * @subpackage                system
 * @category                  Ikarus Framework
 * @license                   GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version                   2.0.0-0001
 */
abstract class AbstractWebApplication extends AbstractApplication {

	/**
	 * @see ikarus\system\application.AbstractApplication::boot()
	 */
	public function boot () {
		parent::boot ();

		RequestDispatcher::getInstance ()->dispatch ($this, $_REQUEST);
	}

	/**
	 * @see ikarus\system\application.AbstractApplication::registerDefaultComponents()
	 */
	public function registerDefaultComponents () {
		parent::registerDefaultComponents ();

		// request components
		Ikarus::requestComponent ('ikarus\system\session\SessionManager', 'SessionManager');
		Ikarus::requestComponent ('ikarus\system\auth\AuthenticationManager', 'AuthenticationManager');
		Ikarus::requestComponent ('ikarus\system\style\StyleManager', 'StyleManager');
		Ikarus::requestComponent ('ikarus\system\language\LanguageManager', 'LanguageManager');
		Ikarus::requestComponent ('ikarus\system\io\WebOutputManager', 'WebOutputManager');

		// configure components
		if ($this->isPrimaryApplication ()) Ikarus::configureComponents ($this);
	}
}

?>