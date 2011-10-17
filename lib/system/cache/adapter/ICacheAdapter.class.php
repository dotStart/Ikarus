<?php
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
	 * @throws			SystemException
	 * @return			boolean
	 */
	public function createResource($resourceName, $cacheFile, $cacheBuilderClass, $minimalLifetime = 0, $maximalLifetime = 0);
	
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
}
?>