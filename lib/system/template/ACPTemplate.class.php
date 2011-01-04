<?php
// cp imports
require_once(IKARUS_DIR.'lib/system/template/Template.class.php');

/**
 * ACPTemplate loads and displays template in the admin control panel of ikarus.
 * ACPTemplate does not support template packs.
 *
 * @author 		Marcel Werk
 * @copyright		2001-2009 WoltLab GmbH
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class ACPTemplate extends Template {
	protected $cachePrefix = 'acp-';

	/**
	 * @see Template::__construct()
	 */
	public function __construct($languageID = 0, $templatePaths = array(), $pluginDir = '', $compileDir = '') {
		if (!$templatePaths) $templatePaths = IKARUS_DIR.'acp/templates/';
		if (!$compileDir) $compileDir = IKARUS_DIR.'acp/templates/compiled/';
		parent::__construct($languageID, $templatePaths, $pluginDir, $compileDir);
	}

	/**
	 * Deletes all compiled acp templates.
	 *
	 * @param 	string		$compileDir
	 */
	public static function deleteCompiledACPTemplates($compileDir = '') {
		if (empty($compileDir)) $compileDir = IKARUS_DIR.'acp/templates/compiled/';

		self::deleteCompiledTemplates($compileDir);
	}
}
?>