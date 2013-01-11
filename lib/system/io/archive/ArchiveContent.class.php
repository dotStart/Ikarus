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
namespace ikarus\system\io\archive;

/**
 * Defines needed methods for archive content specifications.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface ArchiveContent {
	
	/**
	 * Indicates that the content is a file.
	 * @var			integer
	 */
	const TYPE_FILE = 1;
	
	/**
	 * Indicates that the content is a directory.
	 * @var			integer
	 */
	const TYPE_DIRECTORY = 0;
	
	/**
	 * Extracts a file to the specified path.
	 * @param			string			$targetPath
	 * @return			void
	 * @throws			IOException
	 */
	public function extract($targetPath);
	
	/**
	 * Extracts the content of a file and returns it as string.
	 * @return			string
	 * @throws			IOException
	 */
	public function extractContent();
	
	/**
	 * Returns the checksom of the actual file.
	 * @return			mixed
	 */
	public function getChecksum();
	
	/**
	 * Returns the name of the actual file.
	 * @return			string
	 */
	public function getFilename();
	
	/**
	 * Returns the *NIX file mode of the actual file.
	 * @return			integer
	 */
	public function getMode();
	
	/**
	 * Returns the prefix (the path) of the actual file.
	 * @return			string
	 */
	public function getPrefix();
	
	/**
	 * Returns the size of the actual file in bytes.
	 * @return			integer
	 */
	public function getSize();
	
	/**
	 * Returns the type of the actual file (directory or file).
	 * @return			integer
	 */
	public function getType();
	
	/**
	 * Sets the checksum of the actual file.
	 * @param			$checksum			mixed
	 * @return			void
	 */
	public function setChecksum($checksum);
	
	/**
	 * Sets the filename of the actual file.
	 * @param			$handle				ikarus\system\io\FilesystemHandle
	 * @return			void
	 */
	public function setFilename(ikarus\system\io\FilesystemHandle $handle);
	
	/**
	 * Sets the mode of the actual file.
	 * @param			$mode				integer
	 * @return			void
	 */
	public function setMode($mode = 0755);
	
	/**
	 * Sets the prefix of the actual file.
	 * @param			$prefix				string
	 * @return			void
	 */
	public function setPrefix($prefix = '/');
	
	/**
	 * Sets the size of the actual file.
	 * @param			$size				integer			The filesize in bytes.
	 * @return			void
	 */
	public function setSize($size = 0);
	
	/**
	 * Sets the type of the actual file.
	 * @param			$type				integer
	 * @return			void
	 */
	public function setType($type);
}
?>