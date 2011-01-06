<?php

/**
 * Defines default methods for cache builders
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
interface CacheBuilder {

	/**
	 * Returnes the data for new cache file
	 * @param	string	$file
	 * @return mixed
	 */
	public function getData($file);
}
?>