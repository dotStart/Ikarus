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
namespace ikarus\system\style;
use ikarus\system\application\IConfigurableComponent;
use ikarus\system\application\IApplication;
use ikarus\system\event\style\DefaultStyleSetEvent;
use ikarus\system\event\style\StyleEventArguments;
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
class StyleManager implements IConfigurableComponent {

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
	 * (non-PHPdoc)
	 * @see \ikarus\system\application\IConfigurableComponent::configure()
	 */
	public function configure(IApplication $application) {
		$this->application = $application;

		// load cache
		$this->loadStyleCache();

		// set active style
		$this->setActiveStyle(((isset($_REQUEST['styleID']) and $this->getStyle($_REQUEST['styleID']) !== null) ? $this->getStyle($_REQUEST['styleID']) : $this->getDefaultStyle()));
	}

	/**
	 * Returns the current active style.
	 * @return			ikarus\system\style\Style
	 * @api
	 */
	public function getActiveStyle() {
		return $this->activeStyle;
	}

	/**
	 * Returns the default style.
	 * @throws			ikarus\system\exception\SystemException
	 * @return			ikarus\system\style\Style
	 * @api
	 */
	public function getDefaultStyle() {
		foreach($this->styleList as $style) if ($style->isDefault) return $style;
		throw new SystemException('No default style set');
	}

	/**
	 * Returns a style by identifier.
	 * @param			integer			$styleID
	 * @return			ikarus\system\style\Style
	 * @api
	 */
	public function getStyle($styleID) {
		foreach($this->styleList as $style) if ($style->styleID == $styleID) return $style;
		return null;
	}

	/**
	 * Loads all style manager caches.
	 * @return			void
	 */
	protected function loadStyleCache() {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment(), 'styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment(), 'ikarus\system\cache\builder\CacheBuilderStyleList');

		$this->styleList = Ikarus::getCacheManager()->getDefaultAdapter()->get('styleList-'.$this->application->getPackageID().'-'.$this->application->getEnvironment());
	}

	/**
	 * Activates a style.
	 * @param			ikarus\system\style\Style			$style
	 * @return			void
	 * @api
	 */
	public function setActiveStyle(Style $style) {
		// fire event
		$event = new DefaultStyleSetEvent(new StyleEventArguments($style));
		Ikarus::getEventManager()->fire($event);

		// cancellable event
		if ($event->isCancelled()) return;

		$this->activeStyle = $style;
		$this->activeStyle->loadCache();
	}
}
?>