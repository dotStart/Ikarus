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
namespace ikarus\system\io\output;

/**
 * Puts out some fancy JSON (mainly used for our json).
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class JSONOutputHandle extends AbstractOutputHandle implements IOutputHandle {
	
	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\output\IOutputHandle::render()
	 */
	public function render() {
		$data = array_merge($this->outputVariables, $this->data);
		
		// fix encoding
		ArrayUtil::utf8Encode($data);
		
		// return json
		return json_encode($data);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ikarus\system\io\output\IOutputHandle::sendHeaders()
	 */
	public function sendHeaders() {
		Ikarus::getWebOutputManager()->sendHeader('Content-Type', 'application/json');
	}
}
?>