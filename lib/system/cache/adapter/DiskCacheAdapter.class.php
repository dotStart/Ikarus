<?php
namespace ikarus\system\cache\adapter;
use ikarus\system\Ikarus;

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
	 * Contains all stored cache resources
	 * @var			array
	 */
	protected $cacheResources = array();
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::__construct()
	 */
	public function __construct($adapterParameters = array()) { }
	
	/**
	 * Checks wheter a cache file should be rebuilded
	 * @param			string			$cacheFile
	 * @param			string			$cacheBuilderClass
	 * @param			integer			$minimalLifetime
	 * @param			integer			$maximalLifetime
	 * @return			boolean
	 */
	protected function cacheFileNeedsRebuild($cacheFile, $cacheBuilderClass, $minimalLifetime, $maximalLifetime) {
		// check whether the file exists
		if (!Ikarus::getFilesystemManager()->getDefaultAdapter()->fileExists($cacheFile)) return true;
		
		// check for newer cache builders
		// FIXME: This will not work with new caching system
		// if (Ikarus::getFilesystemManager()->getDefaultAdapter()->getModificationTime($cacheFile) < Ikarus::getFilesystemManager()->getDefaultAdapter()->getModificationTime($cacheBuilderClass)) return true;
		
		// check for cache lifetime
		if ($minimalLifetime > 0 and Ikarus::getFilesystemManager()->getDefaultAdapter()->getModificationTime($cacheFile) + $minimalLifetime > TIME_NOW) return false;
		if ($maximalLifetime > 0 and Ikarus::getFilesystemManager()->getDefaultAdapter()->getModificationTime($cacheFile) + $maximalLifetime < TIME_NOW) return true;
		
		// all checks passed
		return false;
	}
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::createResource()
	 */
	public function createResource($resourceName, $cacheFile, $cacheBuilderClass, $minimalLifetime = 0, $maximalLifetime = 0) {
		try {
			$this->storeCacheResource($resourceName, $this->loadCacheFile($cacheFile, $cacheBuilderClass, $minimalLifetime, $maximalLifetime));
		} Catch (SystemException $ex) {
			$this->storeCacheResource($resourceName, $this->storeCacheData($cacheFile, $this->getCacheData($cacheBuilderClass, $resourceName)));
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
	 * Reads data from cache builders
	 * @param			string			$cacheBuilderClass
	 * @param			string			$resourceName
	 * @return			mixed
	 */
	protected function getCacheData($cacheBuilderClass, $resourceName) {
		return call_user_func(array($cacheBuilderClass, 'getData'), $resourceName);
	}
	
	/**
	 * @see ikarus\system\cache\adapter.ICacheAdapter::isSupported()
	 */
	public static function isSupported() {
		return true;
	}
	
	/**
	 * Loads cache data from file
	 * @param			string			$cacheFile
	 * @param			string			$cacheBuilderClass
	 * @param			integer			$minimalLifetime
	 * @param			integer			$maximalLifetime
	 * @throws			SystemException
	 * @returns			mixed
	 */
	protected function loadCacheFile($cacheFile, $cacheBuilderClass, $minimalLifetime, $maximalLifetime) {
		// rebuild if needed
		if ($this->cacheFileNeedsRebuild($cacheFile, $cacheBuilderClass, $minimalLifetime, $maximalLifetime)) throw new SystemException("A rebuild is needed for the cache file '%s'", $cacheFile);
		
		// load information from file
		$fileContent = Ikarus::getFilesystemManager()->getDefaultAdapter()->readFileContents($cacheFile);
		
		// remove file comment and unserialize data
		$fileContent = substr($fileContent, (stripos($fileContent, "\n") + 1));
		$fileContent = unserialize($fileContent);
		
		return $fileContent;
	}
	
	/**
	 * Stores data in cache files
	 * @param			string			$cacheFile
	 * @param			mixed			$data
	 * @return			mixed
	 */
	protected function storeCacheData($cacheFile, $data) {
		// delete old file
		if (Ikarus::getFilesystemManager()->getDefaultAdapter()->fileExists($cacheFile)) Ikarus::getFilesystemManager()->getDefaultAdapter()->deleteFile($cacheFile);
		
		// get handle
		$file = Ikarus::getFilesystemManager()->createFile($cacheFile);
		$file->append("<?php /** Ikarus Framework Cache File (Generated on ".gmdate('r').") **/ die;\n");
		$file->append(serialize($data));
		$file->write();
		
		return $data;
	}
	
	/**
	 * Stores cache data for this script instance
	 * @param			string			$resourceName
	 * @param			mixed			$content
	 * @return			void
	 */
	protected function storeCacheResource($resourceName, $content) {
		$this->cacheResources[$resourceName] = $content;
	}
}
?>