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
namespace ikarus\system\cache\adapter;

/**
 * Defines needed methods for cache adapters
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
interface ICacheAdapter {

	/**
	 * Creates a new instance of type ICacheAdapter
	 * @param			array			$adapterParameters
	 */
	public function __construct($adapterParameters = array());

	/**
	 * Creates a new cache resource for later use
	 * @param			string			$resourceName
	 * @param			string			$cacheFile
	 * @param			string			$cacheBuilderClass
	 * @param			integer			$minimalLifetime
	 * @param			integer			$maximalLifetime
	 * @param			array			$additionalCacheBuilderParameters
	 * @throws			SystemException
	 * @return			boolean
	 */
	public function createResource($resourceName, $cacheFile, $cacheBuilderClass, $minimalLifetime = 0, $maximalLifetime = 0, $additionalCacheBuilderParameters = array());

	/**
	 * Returns the content of given resource
	 * @param			string			$resourceName
	 * @throws			SystemException
	 * @return			mixed
	 */
	public function get($resourceName);

	/**
	 * Returns true if this adapter is supported by php installation
	 * @return			boolean
	 */
	public static function isSupported();

	/**
	 * Closes the cache adapter connection (if any)
	 * @return			void
	 */
	public function shutdown();
}
?>