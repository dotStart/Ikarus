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
namespace ikarus;

/**
 * The PHAR installer template
 * This file is used to create PHAR based installers
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 * @todo		Errr ... Implement?!
 */

// phar extractor
if (!file_exists(dirname(__FILE__).'/setup.phar')) {
	// read this file
	$fp = fopen(__FILE__, 'r');
	
	// get phar content
	fseek($fp, __COMPILER_HALT_OFFSET__);
	
	// get phar content
	$content = stream_get_contents($fp);
	
	// extract phar
	file_put_contents(dirname(__FILE__).'/setup.phar', $content);
}

// start phar
require_once('phar://setup.phar/install.php');

// Stop parser here. Phar follows
__halt_compiler();