<?php
namespace ikarus\system\application;

/**
 * Ikarus Example Application Mode
 * Note: This class also introduces the developer mode
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class IkarusApplication extends AbstractWebApplication {
	
	/**
	 * @see ikarus\system\application.IApplication::__construct()
	 * @throws ApplicationException
	 */
	public function __construct($abbreviation, $libraryPath, $templatePath, $packageID, $environment, $primaryApplication = false) {
		if ($environment != 'administration') throw new ApplicationException("How did you reach this exception? WTF?!");
		parent::__construct($abbreviation, $libraryPath, $packageID, $environment, $primaryApplication);
	}
}
?>