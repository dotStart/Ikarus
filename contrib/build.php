<?php
/**
 * This file is part of the Ikarus Framework.
 * The Ikarus Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * The Ikarus Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Ikarus Framework. If not, see <http://www.gnu.org/licenses/>.
 */
namespace ikarus\contrib;

// defines
define('BUILD_DIR', dirname (__FILE__) . '/');
define('RELATIVE_IKARUS_DIR', '../');

// includes
require_once (RELATIVE_IKARUS_DIR . 'global.php');


// print
printf ("Welcome to the Ikarus " . IKARUS_VERSION . " builder!\n");
printf ("This application will automatically create an installer package based on the source code placed one level higher.\n");
printf ("Please place a .ikarusignore file with ignored directories in this folder if you want to remove specific folders or files from build process.\n");

// open up input
$input = fopen ('php://stdin', 'r');

// start progress
$counter = 5;
do {
	printf ("Starting build process in %u seconds. Press CTRL + C to abort ...\n", $counter);
	$counter--;
	sleep (1);
} while ($counter >= 0);

// seperator
printf ("\n");
printf ("Preparing ...");

// get ignore file
$ignoreFile = "";
if (file_exists (BUILD_DIR . '.ikarusignore')) $ignoreFile = file_get_contents (BUILD_DIR . '.ikarusignore');
$ignores = (!empty($ignoreFile) ? explode ("\n", $ignoreFile) : array());

// append
$ignores[] = 'contrib/';

printf ("OK!\n");

// delete old working copy
if (is_dir (BUILD_DIR . 'build/')) {
	printf ("Cleaning up ...\n");
	cleanUp (new \RecursiveDirectoryIterator(BUILD_DIR . 'build/', \FilesystemIterator::SKIP_DOTS));
	printf ("Everything clean. Starting process ...\n");
}

// create a working copy
print("Creating build directory ... ");
mkdir (BUILD_DIR . 'build/');
print("OK!\n");

// write copies
writeCopy (new \RecursiveDirectoryIterator(RELATIVE_IKARUS_DIR, \FilesystemIterator::SKIP_DOTS));

// check for phar support
if (!extension_loaded ('phar') or (ini_get ('phar.readonly') and !ini_set ('phar.readonly', 0))) {
	$error = fopen ('php://stderr', 'w');
	fputs ($error, "Cannot create installer: PHAR problem: ");
	fputs ($error, (!extension_loaded ('phar') ? 'phar extension not available' : 'phar.readonly set to 1') . "\n");
	exit;
} else
	print("PHAR support detected. Creating 1-file installer!\n");

// get filename
$fileName = BUILD_DIR . 'ikarus-1file-' . str_replace ('.', '', IKARUS_VERSION) . '.tar';

// clean up
if (file_exists ($fileName)) {
	printf ("Cleaning up previous phar for version %s ... ", IKARUS_VERSION);
	unlink ($fileName);
	if (file_exists ($fileName . '.gz')) unlink ($fileName . '.gz');
	print("OK!\n");
}

// create new phar
print("Creating buffered version ... ");
$phar = new \PharData($fileName);
$phar->buildFromDirectory (BUILD_DIR . 'build/');

// set properties
$phar->setMetadata (array('ikarus.version' => IKARUS_VERSION, 'ikarus.version.major' => IKARUS_VERSION_MAJOR, 'ikarus.version.minor' => IKARUS_VERSION_MINOR, 'ikarus.version.revision' => IKARUS_VERSION_REVISION));

// set signature
$phar->setSignatureAlgorithm (\PHAR::SHA512);

// compress
$phar->compress (\PHAR::GZ);
print("OK!\n");

// compile into install.php
$installScript = file_get_contents (BUILD_DIR . 'installer/template/phar/install.php');

// append phar
$installScript .= file_get_contents ($fileName . '.gz');

// remove phar
unlink ($fileName);
unlink ($fileName . '.gz');

// store
file_put_contents (BUILD_DIR . 'install-' . str_replace ('.', '', IKARUS_VERSION) . '.php', $installScript);

/**
 * Cleans up a path or file.
 * @param                        \RecursiveDirectoryIterator $iterator
 */
function cleanUp (\RecursiveDirectoryIterator $iterator) {
	$it = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
	printf ("Found %u elements to clean up!\n", count ($it));

	foreach ($it as $item) {
		// delete file
		if ($item->isFile ()) {
			printf ("Deleting file %s ... ", $item->getBasename ());
			unlink ($item->getRealPath ());
			print("OK\n");
			continue;
		}

		// delete directory
		printf ("Deleting directory %s ... ", $item->getBasename ());
		rmdir ($item->getRealPath ());
		print("OK\n");
	}

	$directoryPath = ($iterator->getRealPath () ? $iterator->getRealPath () : $iterator->getPath ());
	print("Deleting iterator directory ... ");
	rmdir ($directoryPath);
	print("OK!\n");
}

/**
 * Stripps the path from senseless elements.
 * @param                        string $path
 * @return                        string
 */
function getStrippedPath ($path) {
	$elements = explode ('/', $path);

	// remove . & ..
	if ($elements[0] == '..' or $elements[0] == '.') {
		unset($elements[0]);
		$elements = array_merge (array(), $elements);
	}

	return implode ('/', $elements);
}

/**
 * Fixes the file path (re-routes it to build directory)
 * @param                        string $pathName
 * @return                        mixed
 */
function preparePath ($pathName) {
	return \ikarus\util\FileUtil::addTrailingSlash (preg_replace ('~^\.\.~', BUILD_DIR . 'build/', $pathName));
}

/**
 * Creates an exact copy of a directory.
 * @param                        RecursiveDirectoryIterator $iterator
 * @return                        void
 */
function writeCopy (\RecursiveDirectoryIterator $iterator) {
	global $ignores; // uhhh baaaad

	// create iterator
	$iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

	// print
	printf ("OK!\nGot " . count ($iterator) . " elements and " . count ($ignores) . " ignores.\n\n");

	foreach ($iterator as $item) {
		printf ("Copying element %s ... ", $item->getBasename ());

		$path = \ikarus\util\FileUtil::removeLeadingSlash (\ikarus\util\FileUtil::addTrailingSlash (getStrippedPath ($item->getPath ())) . $item->getBasename () . ($item->isDir () ? '/' : ''));
		foreach ($ignores as $ignore) {
			if (preg_match ('~^' . preg_quote ($ignore, '~i') . '~', $path)) {
				printf ("Ignored (matched '%s')\n", $ignore);
				continue 2;
			}
		}

		// create file
		if ($item->isFile ()) {
			file_put_contents (preparePath ($item->getPath ()) . $item->getBasename (), file_get_contents ($item->getPath ()));
			printf ("OK (Wrote File)\n");
			continue;
		}

		// create directory
		mkdir (preparePath ($item->getPath ()) . $item->getBasename ());
		printf ("OK (Created Directory)\n");
	}
}

?>