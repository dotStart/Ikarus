<?php
// ikarus imports
require_once(IKARUS_DIR.'lib/system/style/Style.class.php');

/**
 * Manages styles for environments
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @package		com.develfusion.ikarus
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		1.0.0-0001
 */
class StyleManager {

	/**
	 * Contains the default environment for StyleManager
	 * @var string
	 */
	const DEFAULT_ENVIRONMENT = 'frontend';

	/**
	 * Contains the current active style object
	 * @var Style
	 */
	protected $activeStyle = null;

	/**
	 * Contains the environment of current StyleManager instance
	 * @var string
	 */
	protected $environment = 'frontend';

	/**
	 * Contains all style instances
	 * @var	array
	 */
	protected static $instance = array();

	/**
	 * Creates a new instance of StyleManager
	 */
	protected function __construct($environment) {
		$this->environment = $environment;

		// load cache
		// $this->loadCache($environment);

		// try to set active stle from query parameter
		try {
			$this->setActiveStyle($this->getStyle(intval($_REQUEST['styleID'])));
		} catch (SystemException $ex) {
			$this->setActiveStyle($this->getDefaultStyle());
		}
	}

	/**
	 * Returnes the current active style
	 */
	public function getActiveStyle() {
		return $this->activeStyle;
	}
	
	/**
	 * Returnes the default style
	 * @throws SystemException
	 * @return Style
	 */
	public function getDefaultStyle() {
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_style
			WHERE
				environment = '".escapeString($this->environment)."'
			AND
				isDefault = 1";
		$row = IKARUS::getDatabase()->getFirstRow($sql);
		
		// style not found?
		if (!IKARUS::getDatabase()->countRows()) throw new SystemException("What the hell?! No default Style detected?!");
		
		return (new Style($row));
	}

	/**
	 * Returnes a StyleManager instance
	 * @param	string	$environment
	 */
	public static function getInstance($environment = self::DEFAULT_ENVIRONMENT) {
		if (!isset(self::$instance[$environment])) {
			self::$instance[$environment] = new StyleManager($environment);
		}

		return self::$instance[$environment];
	}
	
	/**
	 * Returnes the style with given ID
	 * @param	integer	$styleID
	 * @throws SystemException
	 * @return Style
	 */
	public function getStyle($styleID) {
		$sql = "SELECT
				*
			FROM
				ikarus".IKARUS_N."_style
			WHERE
				environment = '".escapeString($this->environment)."'
			AND
				styleID = ".$styleID;
		$row = IKARUS::getDatabase()->getFirstRow($sql);
		
		// style not found?
		if (!IKARUS::getDatabase()->countRows()) throw new SystemException("Unknown style with ID '%u'", $styleID);
		
		return (new Style($row));
	}

	/**
	 * Sets a style as active
	 * @param	Style	$style
	 * @throws SystemException
	 */
	public function setActiveStyle(Style $style) {
		// validate given style
		if (!$style->styleID) throw new SystemException("Invalid style element passed to method setActiveStyle");

		// set style
		$this->activeStyle = $style;
	}
}
?>