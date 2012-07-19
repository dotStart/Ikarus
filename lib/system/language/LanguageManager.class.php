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
namespace ikarus\system\language;
use ikarus\system\application\IApplication;
use ikarus\system\application\IConfigurableComponent;
use ikarus\system\exception\SystemException;
use ikarus\system\Ikarus;
use ikarus\system\language\Language;

/**
 * Manages languages and localisations
 * @author		Johannes Donath
 * @copyright		2011 Evil-Co.de
 * @package		de.ikarus-framework.core
 * @subpackage		system
 * @category		Ikarus Framework
 * @license		GNU Lesser Public License <http://www.gnu.org/licenses/lgpl.txt>
 * @version		2.0.0-0001
 */
class LanguageManager implements IConfigurableComponent {
	
	/**
	 * Contains the current choosen language
	 * @var			ikarus\system\language\Language
	 */
	protected $activeLanguage = null;

	/**
	 * Contains the configured application
	 * @var			ikarus\system\application\IApplication
	 */
	protected $application = null;
	
	/**
	 * Contains a list of available languages
	 * @var			array<ikarus\system\language\Language>
	 */
	protected $languageList = null;
	
	/**
	 * Configures a language manager instance
	 * @param			ikarus\system\application\IApplication			$application
	 * @return			void
	 */
	public function configure(IApplication $application) {
		// configure instance
		$this->application = $application;
		
		// load cache
		$this->loadLanguageCache();
		
		// set active language
		$this->setActiveLanguage(((isset($_REQUEST['languageID']) and $this->getLanguage($_REQUEST['languageID']) !== null) ? $this->getLanguage($_REQUEST['languageID']) : $this->getDefaultLanguage()));
	}
	
	/**
	 * Returns the default language
	 * @throws			ikarus\system\exception\SystemException
	 * @return			ikarus\system\language\Language
	 */
	public function getDefaultLanguage() {
		foreach($this->languageList as $language) if ($language->isDefault) return $language;
		throw new SystemException('No default language set');
	}
	
	/**
	 * Returns a language by identifier
	 * @param			integer			$languageID
	 * @return			ikarus\system\language\Language
	 */
	public function getLanguage($languageID) {
		foreach($this->languageList as $language) if ($language->languageID == $languageID) return $language;
		return null;
	}
	
	/**
	 * Loads the language cache
	 * @return			void
	 */
	protected function loadLanguageCache() {
		Ikarus::getCacheManager()->getDefaultAdapter()->createResource('lannguages-'.$this->application->getPackageID(), 'languages-'.$this->application->getPackageID(), 'ikarus\\system\\cache\\builder\\CacheBuilderLanguages');
		
		$this->languageList = Ikarus::getCacheManager()->getDefaultAdapter()->get('lannguages-'.$this->application->getPackageID());
	}
	
	/**
	 * Sets a language as active
	 * @param			ikarus\system\language\Language			$language
	 * @return			void
	 */
	public function setActiveLanguage(Language $language) {
		$this->activeLanguage = $language;
		$this->activeLanguage->loadCache();
	}
}
?>