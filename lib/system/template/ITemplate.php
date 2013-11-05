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
namespace ikarus\system\template;

/**
 * Class ITemplate
 * @author			Johannes Donath
 * @copyright			Copyright (C) 2013 Evil-Co <http://www.evil-co.com>
 * @package			ikarus\system\template
 * @category			Ikarus Framework
 * @license			GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version			2.0.0-0001
 */
interface ITemplate {

	/**
	 * Returns a cached version of the template.
	 * @return ICachedTemplate
	 */
	public function getCachedTemplate ();

	/**
	 * Returns the complete file content as a string.
	 * @return string
	 */
	public function getFileContent ();

	/**
	 * Returns a file info object for this template file.
	 * @return IFileInfo
	 */
	public function getFileInfo ();

	/**
	 * Checks whether this template needs a refresh.
	 * @return boolean
	 */
	public function refreshNeeded ();

	/**
	 * Validates the template file.
	 * @param ICompiler $compiler
	 * @return void
	 * @throws CompilerException
	 */
	public function validate (ICompiler $compiler);
}
?>