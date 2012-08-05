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
namespace ikarus\system\event\extension;
use ikarus\system\event\extension\CancellableExtensionEvent;

/**
 * This event occurs if the output has been gzipped.
 * @author		Johannes Donath
 * @copyright		2012 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class OutputGzippedEvent extends CancellableExtensionEvent {

	/**
	 * Contains a replacement for handled output buffer.
	 * @var			string
	 */
	protected $replacement = null;

	/**
	 * Constructs the object.
	 * @param			BufferEventArguments			$arguments
	 */
	public function __construct(BufferEventArguments $arguments) {
		parent::__construct($arguments);
	}

	/**
	 * Returns the set replacement for handled output buffer.
	 * @return			string
	 */
	public function getReplacement() {
		return $this->replacement;
	}

	/**
	 * Sets a replacement for handled buffer.
	 * @param			string			$buffer
	 */
	public function setReplacement($buffer) {
		$this->replacement = $buffer;
	}
}
?>