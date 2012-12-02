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
namespace ikarus\system\io;
use ikarus\system\exception\io\IOException;

/**
 * Allows to access files.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class File {

	/**
	 * Contains the filename
	 * @var			string
	 */
	protected $filename = '';

	/**
	 * Contains the file resource
	 * @var			resource
	 */
	protected $resource = null;
	
	/**
	 * Stores the resource mode to use.
	 * @var			string
	 */
	protected $resourceMode = '';
	
	/**
	 * Opens a new file.
	 *
	 * @param 		string			$filename
	 * @param 		string			$mode
	 * @throws		IOException
	 */
	public function __construct($filename, $mode = 'wb') {
		$this->filename = $filename;
		$this->resourceMode = $mode;
		
		$this->open();
	}
	
	/**
	 * Opens up the file.
	 * @return			void
	 * @throws			IOException
	 */
	public function open() {
		// open resource
		$this->resource = fopen($this->filename, $this->resourceMode);
		
		// validate
		if ($this->resource === false) throw new IOException('Cannot open file "%s" in mode %s', $this->filename, $this->resourceMode);
	}

	/**
	 * Returnes the given filename
	 * @return			string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * Returns the current file handle
	 * @return			Resource
	 * @deprecated			Everything should be wrapped in some way.
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * Allows to call file methods without any need of direct implementation.
	 * @param 			string			$function
	 * @param 			array			$arguments
	 * @return			mixed
	 */
	public function __call($function, $arguments) {
		if (function_exists('f' . $function)) {
			// append resource as parameter
			array_unshift($arguments, $this->resource);
			
			// call f<method>()
			return call_user_func_array('f' . $function, $arguments);
		} elseif (function_exists($function)) {
			// append filename as parameter
			array_unshift($arguments, $this->filename);
			
			// call <method>()
			return call_user_func_array($function, $arguments);
		} else
			throw new NotImplementedException('Called unsupported method "%s" from file context', $function);
	}
}
?>