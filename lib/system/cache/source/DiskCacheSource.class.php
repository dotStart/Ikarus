<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/cache/AbstractCacheSource.class.php');

/**
 * Provides a cache builder that stores his data on disk
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class DiskCacheSource extends AbstractCacheSource {

	/**
	 * @see lib/system/cache/CacheSource::get()
	 */
	function get($cacheFile, $cacheBuilderPath, $minLifetime, $maxLifetime) {
		if ($this->needsRebuild($cacheFile, $minLifetime, $maxLifetime))
			return $this->rebuildFile($cacheFile, $cacheBuilderPath);
		else
			return $this->readFile($cacheFile);
	}
	
	/**
	 * Returnes true if a cache file should rebuilded
	 * @param	string	$cacheFile
	 * @param	integer	$minLifetime
	 * @param	integer	$maxLifetime
	 * @return boolean
	 */
	protected function needsRebuild($cacheFile, $minLifetime, $maxLifetime) {
		// file does not exist?
		if (!file_exists($cacheFile)) return true;
		
		// is readable?
		if (!is_readable($cacheFile)) return true;
		
		// check timestamp (min life time)
		if ((filemtime($cacheFile) + $maxLifetime) < TIME_NOW) return false;
		
		// check timestamp (max life time)
		if ((filemtime($cacheFile) + $maxLifetime) < TIME_NOW) return true;
		
		// return false
		return false;
	}
	
	/**
	 * Reads a cache file
	 * @param	string	$cacheFile
	 * @throws SystemException
	 * @return mixed
	 */
	protected function readFile($cacheFile) {
		// validate filename
		if (!file_exists($cacheFile)) throw new SystemException("Cannot read cache file '%s'", $cacheFile);
		
		// kill system if cache isn't readable
		if (!is_readable($cacheFile)) throw new SystemException("Cannot read cache file '%s'", $cacheFile);
		
		// get cache content
		$cache = file_get_contents($cacheFile);
		
		// strip php code
		$cache = substr($cache, (stripos($cache, "\n") + 1));
		
		// unserialize data
		$data = unserialize($cache);
		
		return $data;
	}
	
	/**
	 * Rebuilds a cache file
	 * @param	string	$cacheFile
	 * @param	string	$cacheBuilderPath
	 * @throws SystemException
	 * @return mixed
	 */
	protected function rebuildFile($cacheFile, $cacheBuilderPath) {
		// delete file
		@unlink($cacheFile);
		
		// include cache builder
		if (!file_exists($cacheBuilderPath)) throw new SystemException("Cannot load cache builder '%s'", $cacheBuilderPath);
		require_once($cacheBuilderPath);
		
		// start cache builder
		$className = basename($cacheBuilderPath, '.class.php');
		$cacheBuilder = new $className();
		
		// validate cache builder
		if (!($cacheBuilder instanceof CacheBuilder)) throw new SystemException("Invalid cache builder: %s", $cacheBuilderPath);
		
		// create new file object
		$file = new File($cacheFile);
		
		// write file header
		$file->write("<?php /** Ikarus Cache File (Generated on ".gmdate('r').") **/ exit; ?>\n");
		
		// get content
		$data = $cacheBuilder->getData($cacheFile);
		
		// write content
		$file->write(serialize($data));
		
		// close file
		$file->close();
		
		// return data
		return $data;
	}
}
?>