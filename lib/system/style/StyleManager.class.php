<?php
namespace ikarus\system\style;
use ikarus\system\application\IApplication;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;

/**
 * 
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class StyleManager {
	
	/**
	 * Contains the current activated style
	 * @var			ikarus\system\style\Style
	 */
	protected $activeStyle = null;
	
	/**
	 * Contains the active application
	 * @var			ikarus\system\application\IApplication
	 */
	protected $application = null;
	
	/**
	 * Contains a cached list of styles
	 * @var			array<ikarus\system\style\Style>
	 */
	protected $styleList = array();
	
	/**
	 * Configures the style instance
	 * @param			ikarus\system\application\IApplication			$application
	 * @return			void
	 */
	public function configure(IApplication $application) {
		$this->application = $application;
		
		// load cache
		$this->loadStyleCache();
		
		// set active style
		$this->setActiveStyle(((isset($_REQUEST['styleID']) and $this->getStyle($_REQUEST['styleID']) !== null) ? $this->getStyle($_REQUEST['styleID']) : $this->getDefaultStyle()));
	}
	
	/**
	 * Returns the current active style
	 * @return			ikarus\system\style\Style
	 */
	public function getActiveStyle() {
		return $this->activeStyle;
	}
	
	/**
	 * Returns the default style
	 * @throws			ikarus\system\exception\SystemException
	 * @return			ikarus\system\style\Style
	 */
	public function getDefaultStyle() {
		foreach($this->styleList as $style) if ($style->isDefault) return $style;
		throw new SystemException('No default style set');
	}
	
	/**
	 * Returns a style by identifier
	 * @param			integer			$styleID
	 * @return			ikarus\system\style\Style
	 */
	public function getStyle($styleID) {
		foreach($this->styleList as $style) if ($style->styleID == $styleID) return $style;
		return null;
	}
	
	/**
	 * Loads all style manager caches
	 * @return			void
	 */
	protected function loadStyleCache() {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment(), 'styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment(), 'ikarus\system\cache\builder\CacheBuilderStyleList');
	
		$this->styleList = Ikarus::getCacheManager()->getDefaultAdapter()->get('styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment());
	}
	
	/**
	 * Activates a style
	 * @param			ikarus\system\style\Style			$style
	 * @return			void
	 */
	public function setActiveStyle(Style $style) {
		$this->activeStyle = $style;
		$this->activeStyle->loadCache();
	}
}
?>