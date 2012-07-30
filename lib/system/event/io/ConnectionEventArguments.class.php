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
namespace ikarus\system\event\io;
use ikarus\system\event\AbstractEventArguments;

/**
 * Stores connection information.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class ConnectionEventArguments extends AbstractEventArguments {

	/**
	 * Constructs the object.
	 * @param			string			$adapterName
	 * @param			array			$adapterParameters
	 * @param			string			$linkID
	 */
	public function __construct($adapterName, $adapterParameters, $linkID) {
		$this->content['adapterName'] = $adapterName;
		$this->content['adapterParameters'] = $adapterParameters;
		$this->content['linkID'] = $linkID;
	}
}
?>