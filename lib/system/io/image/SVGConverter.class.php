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
namespace ikarus\system\io\image;

use ikarus\system\exception\MissingDependencyException;

/**
 * Allows to convert SVGs for cross browser support.
 * @author		Johannes Donath
 * @copyright		© Copyright 2012 Evil-Co.de <http://www.evil-co.com>
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class SVGConverter {
	
	/**
	 * Converts a SVG to PNG32 and returns it as string.
	 * @param			string			$svg
	 * @param			integer			$width
	 * @param			integer			$height
	 * @return			string
	 */
	public static function convert($svg, $width = 24, $height = 24) {
		// validate
		if (!static::isSupported()) throw new MissingDependencyException("Converting SVGs is not supported by PHP");
		
		// render
		$image = new Imagick();
		$image->setBackgroundColor(new ImagickPixel('transparent'));
		$image->readImageBlob($svg);
		$image->scaleImage($width, $height);
		$image->setImageFormat('png32');
		return $image->getImageBlob();
	}
	
	/**
	 * Checks whether the PHP installation is able to read SVGs.
	 * @return			boolean
	 */
	public static function isSupported() {
		return (extension_loaded('imagick'));
	}
}
?>