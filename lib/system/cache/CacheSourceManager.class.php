<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/cache/CacheSource.class.php');
require_once(IKARUS_DIR.'lib/system/cache/AbstractCacheSource.class.php');

/**
 * Manages cache sources
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class CacheSourceManager {

	/**
	 * Contains a list of all available cache sources (All cache sources stored in lib/system/cache/source/ will automaticly loaded and added to this list)
	 * @var array<CacheSource>
	 */
	protected $cacheSources = array();

	/**
	 * Contains the fallback cache source (This will used if a class uses a non-loaded cache source)
	 * @var	CacheSource
	 */
	protected $fallbackCacheSource = null;

	/**
	 * Creates a new instance of CacheSourceManager
	 * @param	string	$fallbackCacheSourceName
	 */
	public function __construct($fallbackCacheSourceName, $disabledCacheSources = array()) {
		// call init methods
		$this->loadCacheSources();
		$this->enableCacheSources($disabledCacheSources);
		$this->chooseFallbackCacheSource($fallbackCacheSourceName);
	}

	/**
	 * Loads all available cache sources
	 * Note: This will automaticly add all sources located in lib/system/cache/source/
	 */
	protected function loadCacheSources() {
		$dirIterator = new DirectoryIterator(IKARUS_DIR.'lib/system/cache/source/');

		// loop through iterator index
		foreach($dirIterator as $dir) {
			if ($dir->isFile()) {
				// load definition
				require_once($dir->getPathname());

				// create new instance
				$className = $dir->getBasename('.class.php');

				// add to available cache sources array
				$this->cacheSources[str_replace('CacheSource', '', $dir->getBasename('.class.php'))] = new $className();
			}
		}
	}

	/**
	 * Enables all loaded sources
	 * @param	array<string>	$disabledCacheSources
	 */
	protected function enableCacheSources($disabledCacheSources) {
		foreach($this->cacheSources as $key => $source) {
			if (!in_array($key, $disabledCacheSources) and $this->cacheSources[$key]->isSupported()) $this->cacheSources[$key]->enable();
		}
	}

	/**
	 * Sets a fallback cache source
	 * @param	string	$fallbackCacheSourceName
	 */
	protected function chooseFalbackCacheSource($fallbackCacheSourceName) {
		$this->fallbackCacheSource = $this->getCacheSource($fallbackCacheSourceName);

		// validate
		if (!$this->fallbackCacheSource) throw new SystemException("Unable to load fallback cache source '%s'", $fallbackCacheSourceName);
	}

	/**
	 * Returnes the instance of the given cache source
	 * @param	string	$sourceName
	 * @return mixed
	 */
	public function getCacheSource($sourceName) {
		foreach($this->cacheSources as $key => $source) {
			if ($key == $sourceName) return $this->cacheSources[$key];
		}

		return false;
	}

	/**
	 * Reads cache content from given source
	 * @param	string	$cacheFile
	 * @param	string	$cacheBuilderPath
	 * @param	mixed	$minLifetime
	 * @param	mixed	$maxLifetime
	 * @param	mixed	$source
	 * @return mixed
	 */
	public function get($cacheFile, $cacheBuilderPath, $minLifetime = false, $maxLifetime = false, $source = null) {
		// handle fallback
		if (!$source) $source = $this->fallbackCacheSource;

		// get sources
		if (!($source instanceof CacheSource)) $source = $this->getCacheSource($source);

		// call source method
		return $source->get($cacheFile, $cacheBuilderPath, $minLifetime, $maxLifetime);
	}

	/**
	 * Flushes all cache sources
	 */
	public function flush() {
		foreach($this->cacheSources as $key => $source) {
			if ($this->cacheSources[$key]->isEnabled()) $this->cacheSources[$key]->flush();
		}
	}
}
?>