<?php
namespace ikarus\system\cache\adapter;

/**
 * Provides a cache builder that stores its data on disk
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class DiskCacheAdapter implements ICacheAdapter {
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::__construct()
	 */
	public function __construct($adapterParameters = array()) { }
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::createResource()
	 */
	public function createResource($resourceName, $cacheFile, $cacheBuilderFile, $minimalLifetime = 0, $maximalLifetime = 0) {
		try {
			$this->storeCacheResource($resourceName, $this->loadCacheFile($cacheFile, $cacheBuilderFile, $minimalLifetime, $maximalLifetime));
		} Catch (SystemException $ex) {
			$this->storeCacheResource($resourceName, $this->storeCacheData($this->getCacheData($cacheBuilderFile)));
		}
		return true;
	}
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::get()
	 */
	public function get($resourceName) {
		// validate cache resource
		if (!array_key_exists($resourceName, $this->cacheResources)) throw new StrictStandardException("Tried to access unknown cache resource '%s'", $resourceName);
		
		// return data
		return $this->cacheResources[$resourceName];
	}
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::isSupported()
	 */
	public static function isSupported() {
		return true;
	}
}
?>