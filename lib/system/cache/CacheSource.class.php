<?php

/**
 * Defines default methods for cache sources
 * Note: CacheSourceManager will only accept instances of this interface!
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage	system
 * @category	Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface CacheSource {

	/**
	 * Returnes true if this cache source is supported on current system
	 */
	public function isSupported();

	/**
	 * Enables this cache source
	 */
	public function enable();

	/**
	 * Returnes the content of the defined cache file
	 * @param	string	$cacheFile
	 * @param	string	$cacheBuilderPath
	 * @param	mixed	$minLifetime
	 * @param	mixed	$maxLifetime
	 * @return mixed
	 * @throws SystemException
	 */
	public function get($cacheFile, $cacheBuilderPath, $minLifetime, $maxLifetime);
}
?>