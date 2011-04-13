<?php

/**
 * Handles requests
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class RequestHandler {

	/**
	 * @param	string	$name
	 * @param	string	$type
	 * @param	string	$packageDir
	 * @throws SystemException
	 */
	protected function __construct($name, $type, $packageDir) {
		// validate classname
		if (!preg_match('/^[a-z0-9_]+$/i', $name)) throw new SystemException("Nice classname my friend!");

		// generate path
		$file = IKARUS_DIR.$packageDir.$type.'/'.$name.ucfirst($type).'.class.php';

		// validate file
		if (!file_exists($file)) throw new SystemException("No file no fun!");

		// include class
		require_once($file);

		// get class name
		$className = basename($file, '.class.php');

		// start page
		new $className();
	}

	/**
	 * Handles a request
	 * @param	array<string>	$packageDirs
	 */
	public function handle($packageDirs) {
		try {
			foreach($packageDirs as $dir) {
				foreach($_REQUEST as $type => $name) {
					$type = strtoupper($type);
					switch($type) {
						case 'PAGE':
							$foundType = 'page';
							$foundName = $name;
							break;
						case 'ACTION':
							$foundType = 'page';
							$foundName = $name;
							break;
						case 'FORM':
							$foundType = 'page';
							$foundName = $name;
							break;
						case 'ZEUCH':
							throw new NamedUserException("ZEUCH, TEICH, BAUM and KARTOFFELSALAT!");
							break;
					}
				}

				// fallback
				if (!isset($foundType) or !isset($foundName)) {
					$foundType = 'page';
					$foundName = 'Index';
				}

				// call __construct method
				new RequestHandler($foundName, $foundType, $dir);
			}
		} catch(Exception $ex) {
			throw new IllegalLinkException;
		}
	}
}
?>