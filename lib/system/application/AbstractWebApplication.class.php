<?php
namespace ikarus\system\application;
use ikarus\system\request\RequestDispatcher;
use ikarus\system\session\SessionManager;
use ikarus\system\style\StyleManager;
use ikarus\system\template\Template;

/**
 * Implements an application that loads components that are often used in web applications
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
abstract class AbstractWebApplication extends AbstractApplication {
	
	/**
	 * @see ikarus\system\application.AbstractApplication::boot()
	 */
	public function boot() {
		parent::boot();
		
		RequestDispatcher::getInstance()->dispatch($this, $_REQUEST);
	}
	
	/**
	 * @see ikarus\system\application.AbstractApplication::registerDefaultComponents()
	 */
	public function registerDefaultComponents() {
		parent::registerDefaultComponents();
		
		$this->addComponent('SessionManager', new SessionManager($this, $this->environment));
		$this->addComponent('LanguageManager', new LanguageManager($this));
		$this->addComponent('Template', new Template($this, $this->environment));
		$this->addComponent('StyleManager', new StyleManager($this, $this->environment));
	}
}
?>